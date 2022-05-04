<?php

namespace App\Controller;

use App\Entity\UserAddress;
use App\Service\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAddressController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/users/addresses', methods: "GET", name: 'app_user_address')]
    public function getAdresses(Request $request, ApiResponse $apiResponse): Response
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
        $companies = $this->em->getRepository(UserAddress::class)->findAllByUser($userId);
        return $this->json($companies);
    }
    
    #[Route('/users/addresses/last', methods: "GET", name: 'app_user_last_address')]
    public function getLastAdresses(Request $request, ApiResponse $apiResponse): Response
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
        $companies = $this->em->getRepository(UserAddress::class)->findLastByUser($userId);
        return $this->json($companies);
    }
}
