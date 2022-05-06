<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserAddress;
use App\Service\ApiResponse;
use DateTimeImmutable;
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
        $companies = $this->em->getRepository(UserAddress::class)->findAllByUser($userId);
        return $this->json($companies);
    }

    #[Route('/users/addresses', methods: "POST")]
    public function addAdresses(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "user_id" => [
                "type" => "integer",
            ],
            "name" => [
                "type" => "string",
                "required" => false
            ],
            "address_line1" => [
                "type" => "string",
            ],
            "address_line2" => [
                "type" => "string",
                "required" => false
            ],
            "city" => [
                "type" => "string",
            ],
            "postal_code" => [
                "type" => "string",
            ],
            "country" => [
                "type" => "string",
            ],
            "phone_number" => [
                "type" => "string",
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $userAddress = new UserAddress();

        $user = $this->em->getRepository(User::class)->find($response["user_id"]);
        $userAddress->setUserId($user);
        
        $name = $response["name"] ?? null;
        if ($name) $userAddress->setName($name);

        $userAddress->setAddressLine1($response["address_line1"]);

        $addressLine2 = $response["address_line2"] ?? null;
        if ($addressLine2) $userAddress->setAddressLine2($addressLine2);

        $userAddress->setCity($response["city"]);
        $userAddress->setPostalCode($response["postal_code"]);
        $userAddress->setCountry($response["country"]);
        $userAddress->setPhoneNumber($response["phone_number"]);

        $userAddress->setCreatedAt(new DateTimeImmutable());
        $userAddress->setUpdatedAt(new DateTimeImmutable());

        $this->em->persist($userAddress);
        $this->em->flush();
        return $this->json(true);
    }
    
    #[Route('/users/addresses/{id}', methods: "POST")]
    public function updateAdresses(int $id, Request $request, ApiResponse $apiResponse): Response
    {
        $userAddress = $this->em->getRepository(UserAddress::class)->find($id);

        if (empty($userAddress)) {
            return $this->json("Doesn't exist", 404);
        }

        $params = [
            "name" => [
                "type" => "string",
                "required" => false
            ],
            "address_line1" => [
                "type" => "string",
                "required" => false
            ],
            "address_line2" => [
                "type" => "string",
                "required" => false
            ],
            "city" => [
                "type" => "string",
                "required" => false
            ],
            "postal_code" => [
                "type" => "string",
                "required" => false
            ],
            "country" => [
                "type" => "string",
                "required" => false
            ],
            "phone_number" => [
                "type" => "string",
                "required" => false
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        

        $name = $response["name"] ?? null;
        if ($name) $userAddress->setName($name);

        $addressLine1 = $response["address_line1"] ?? null;
        if ($addressLine1) $userAddress->setAddressLine1($addressLine1);

        $addressLine2 = $response["address_line2"] ?? null;
        if ($addressLine2) $userAddress->setAddressLine2($addressLine2);

        $city = $response["city"] ?? null;
        if ($city) $userAddress->setCity($city);

        $postalCode = $response["postal_code"] ?? null;
        if ($postalCode) $userAddress->setPostalCode($postalCode);

        $country = $response["country"] ?? null;
        if ($country) $userAddress->setCountry($country);

        $phoneNumber = $response["phone_number"] ?? null;
        if ($phoneNumber) $userAddress->setPhoneNumber($phoneNumber);

        $userAddress->setSelectedAt(New DateTimeImmutable());
        $userAddress->setUpdatedAt(New DateTimeImmutable());

        $this->em->persist($userAddress);
        $this->em->flush();

        return $this->json(true);
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
