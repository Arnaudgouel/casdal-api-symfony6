<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/orders/users', methods: ["GET"], name: 'app_orders_users')]
    public function findOrdersUser(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "company_id" => "integer"
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isParamsExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $companyId = $response["company_id"];
        $products = $this->em->getRepository(Order::class)->findOrdersByCompany($companyId);

        return $this->json($products, 200, ['Content-Type' => 'application/json']);
    }
}
