<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\CompanyAddress;
use App\Service\ApiResponse;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyAddressController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/companies/addresses', methods: "GET")]
    public function getAdresses(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "company_id" => [
                "type" => "integer"
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isParamsExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $companyId = $response["company_id"];
        $companyAddress = $this->em->getRepository(CompanyAddress::class)->findBy(["company_id" => $companyId]);
        return $this->json($companyAddress);
    }

    #[Route('/companies/addresses', methods: "POST")]
    public function addAdresses(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "company_id" => [
                "type" => "integer",
            ],
            "name" => [
                "type" => "string",
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
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $companyAddress = new CompanyAddress();

        $company = $this->em->getRepository(Company::class)->find($response["company_id"]);
        $companyAddress->setCompanyId($company);
        
        $name = $response["name"];
        $companyAddress->setName($name);

        $companyAddress->setAddressLine1($response["address_line1"]);

        $addressLine2 = $response["address_line2"] ?? null;
        if ($addressLine2) $companyAddress->setAddressLine2($addressLine2);

        $companyAddress->setCity($response["city"]);
        $companyAddress->setPostalCode($response["postal_code"]);
        $companyAddress->setCountry($response["country"]);

        $companyAddress->setCreatedAt(new DateTimeImmutable());
        $companyAddress->setUpdatedAt(new DateTimeImmutable());

        $this->em->persist($companyAddress);
        $this->em->flush();
        return $this->json(true);
    }
    
    #[Route('/companies/addresses/{id}', methods: "POST")]
    public function updateAdresses(int $id, Request $request, ApiResponse $apiResponse): Response
    {
        $companyAddress = $this->em->getRepository(CompanyAddress::class)->find($id);

        if (empty($companyAddress)) {
            return $this->json("Doesn't exist", 404);
        }

        $params = [
            "company_id" => [
                "type" => "integer",
                "required" => false
            ],
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
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        

        $companyId = $response["company_id"] ?? null;
        if ($companyId) {
            $company = $this->em->getRepository(Company::class)->find($companyId);
            $companyAddress->setCompanyId($company);
        }

        $name = $response["name"] ?? null;
        if ($name) $companyAddress->setName($name);

        $addressLine1 = $response["address_line1"] ?? null;
        if ($addressLine1) $companyAddress->setAddressLine1($addressLine1);

        $addressLine2 = $response["address_line2"] ?? null;
        if ($addressLine2) $companyAddress->setAddressLine2($addressLine2);

        $city = $response["city"] ?? null;
        if ($city) $companyAddress->setCity($city);

        $postalCode = $response["postal_code"] ?? null;
        if ($postalCode) $companyAddress->setPostalCode($postalCode);

        $country = $response["country"] ?? null;
        if ($country) $companyAddress->setCountry($country);

        $companyAddress->setUpdatedAt(New DateTimeImmutable());

        $this->em->persist($companyAddress);
        $this->em->flush();

        return $this->json(true);
    }
}
