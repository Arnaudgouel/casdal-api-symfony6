<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Service\ApiResponse;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/orders/users', methods: ["GET"], name: 'app_orders_users')]
    public function findOrdersUser(Request $request, ApiResponse $apiResponse): Response
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
        $products = $this->em->getRepository(Order::class)->findOrdersByUser($userId);

        return $this->json($products, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/orders', methods: ["POST"])]
    public function addNewOrder(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "user_id" => [
                "type" => "integer",
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }

        $user = $this->em->getRepository(User::class)->find($response["user_id"]);

        $order = new Order();

        $date = new DateTime();
        $reference = $date->format('Ymd').uniqid();

        $order->setReference($reference);
        $order->setUserId($user);
        $total = 0;
        $order->setTotal($total);
        $order->setCreatedAt(new DateTimeImmutable());
        $order->setUpdatedAt(new DateTimeImmutable());

        $this->em->persist($order);
        
        $items = $this->em->getRepository(CartItem::class)->findBy(["userId" => $response["user_id"]]);

        if (empty($items)) {
            return $this->json(false, 400);
        }
        
        foreach ($items as $item) {
            $orderItem = new OrderItem();
            $orderItem->setOrderId($order);
            $orderItem->setProductId($item->getProductId());
            $orderItem->setQuantity($item->getQuantity());
            $orderItem->setCreatedAt(new DateTimeImmutable());
            $orderItem->setUpdatedAt(new DateTimeImmutable());
            $total += $item->getProductId()->getPrice() * $item->getQuantity();
            
            $this->em->persist($orderItem);
            
            $this->em->remove($item);
        }
        $order->setTotal($total);
        $this->em->persist($order);

        $this->em->flush();

        return $this->json(count($items));
    }
}
