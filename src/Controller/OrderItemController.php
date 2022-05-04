<?php

namespace App\Controller;

use App\Entity\OrderItem;
use App\Service\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderItemController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/orders/items', methods: ["GET"], name: 'app_order_items_order')]
    public function findOrdersUser(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "order_id" => "integer"
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isParamsExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $orderId = $response["order_id"];
        $products = $this->em->getRepository(OrderItem::class)->findByOrder($orderId);

        return $this->json($products, 200, ['Content-Type' => 'application/json']);
    }
}
