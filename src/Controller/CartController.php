<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Service\ApiResponse;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/cart/items', methods: ["GET"], name: 'app_cart_items')]
    public function findCartItemUser(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "user_id" => [
                "type" => "integer"
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isParamsExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $userId = $response["user_id"];
        $products = $this->em->getRepository(CartItem::class)->findByCartUser($userId);
        $total = $this->em->getRepository(CartItem::class)->findTotalCart($userId);

        return $this->json([
            "products" => $products,
            "total" => $total
        ], 
            200, ['Content-Type' => 'application/json']);
    }

    #[Route('/cart/items', methods: ["POST"])]
    public function addCartItem(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "user_id" => [
                "type" => "integer",
            ],
            "product_id" => [
                "type" => "integer",
            ],
            "quantity" => [
                "type" => "integer",
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }

        $companyId = $this->em->getRepository(Product::class)->find($response["product_id"])->getCompanyId()->getId();

        $isInSameCompany = $this->em->getRepository(CartItem::class)->findItemsOfOtherCompanies($response["user_id"], $companyId);

        $user = $this->em->getRepository(User::class)->find($response["user_id"]);

        if (!$isInSameCompany) {
            $cart = $this->em->getRepository(CartItem::class)->findBy(["userId" => $user]);
            foreach ($cart as $oneItem ) {
                $this->em->remove($oneItem);
            }
        }

        $product = $this->em->getRepository(Product::class)->find($response["product_id"]);

        $oldCartItem = $this->em->getRepository(CartItem::class)->findOneBy(["userId" => $user, "productId" => $product]);


        if ($oldCartItem) {
            $oldCartItem->setQuantity($oldCartItem->getQuantity() + intval($response["quantity"]));

            $this->em->persist($oldCartItem);
            $this->em->flush();
            $total = $this->em->getRepository(CartItem::class)->findTotalCart($user->getId());

            return $this->json([
                "total" => $total
            ], 
                200, ['Content-Type' => 'application/json']);
        }
  

        $cartItem = new CartItem();

        $cartItem->setuserId($user);
        $cartItem->setProductId($product);
        
        $cartItem->setQuantity($response["quantity"]);

        $cartItem->setCreatedAt(new DateTimeImmutable());
        $cartItem->setUpdatedAt(new DateTimeImmutable());

        $this->em->persist($cartItem);
        $this->em->flush();
        $total = $this->em->getRepository(CartItem::class)->findTotalCart($user->getId());
        return $this->json([
            "total" => $total
        ], 
            200, ['Content-Type' => 'application/json']);
    }

    #[Route('/cart/items/{id}', methods: ["POST"])]
    public function updateCartItem(int $id, Request $request, ApiResponse $apiResponse): Response
    {
        $cartItem = $this->em->getRepository(CartItem::class)->find($id);

        if (empty($cartItem)) {
            return $this->json("Doesn't exist", 404);
        }

        $params = [
            "quantity" => [
                "type" => "integer",
            ],
        ];

        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }

        $quantity = $response["quantity"];

        if (0 === intval($quantity)) {
            $this->em->remove($cartItem);
            $this->em->flush();
            return $this->json(0);
        }

        $cartItem->setQuantity($quantity);
        
        $cartItem->setUpdatedAt(New DateTimeImmutable());
        
        $this->em->persist($cartItem);
        $this->em->flush();
        return $this->json(true);
    }
}
