<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\ShoppingSession;
use App\Service\ApiResponse;
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
            "user_id" => "integer"
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

    #[Route('/cart', methods: ["GET"], name: 'app_cart')]
    public function findCartUser(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "user_id" => "integer"
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isParamsExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $userId = $response["user_id"];
        $products = $this->em->getRepository(ShoppingSession::class)->findByOrder($userId);

        return $this->json($products, 200, ['Content-Type' => 'application/json']);
    }
}
