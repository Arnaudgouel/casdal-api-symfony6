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

        return $this->json($products, 200, ['Content-Type' => 'application/json']);
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

        if (!$isInSameCompany) {
            return $this->json(false);
        }

        $cartItem = new CartItem();

        $user = $this->em->getRepository(User::class)->find($response["user_id"]);
        $cartItem->setuserId($user);
        $product = $this->em->getRepository(Product::class)->find($response["product_id"]);
        $cartItem->setProductId($product);
        
        $cartItem->setQuantity($response["quantity"]);

        $cartItem->setCreatedAt(new DateTimeImmutable());
        $cartItem->setUpdatedAt(new DateTimeImmutable());

        $this->em->persist($cartItem);
        $this->em->flush();
        return $this->json(true);
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
