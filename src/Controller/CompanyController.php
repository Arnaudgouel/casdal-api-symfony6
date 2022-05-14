<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\CompanyAddress;
use App\Entity\CompanyCategory;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\User;
use App\Service\ApiResponse;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/companies', methods: ["GET"], name: 'app_companies')]
    public function findCompanies(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "search" => [
                "type" => "string",
                "required" => false
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isParamsExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, [
                'Content-Type' => 'application/json',
                "Access-Control-Allow-Origin" => "*",
                "Access-Control-Allow-Methods" => "GET, POST, OPTIONS, PUT, DELETE"
            ]);
        }

        $search = $response["search"] ?? false;

        if ($search) {
            $companies = $this->em->getRepository(Company::class)->findAllActiveWithSearch($search);
        } else {
            $companies = $this->em->getRepository(Company::class)->findAllActive();
        }

        return $this->json($companies, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/companies/category', methods: ["GET"], name: 'app_company_category')]
    public function allInOneCategory(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "category_id" => [
                "type" => "integer"
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isParamsExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $categoryId = $response["category_id"];
        $companies = $this->em->getRepository(Company::class)->findAllActiveInCategory($categoryId);
        return $this->json($companies);
    }

    #[Route('/companies/user/{id}', methods: ["GET"], name: 'app_company_owned_by_user')]
    public function allCompaniesManagedByUser(int $id): Response
    {
        $companies = $this->em->getRepository(Company::class)->findAllActiveCompaniesManagedByUser($id);
        return $this->json($companies);
    }

    #[Route('/companies/products', methods: ["GET"], name: 'app_company_products')]
    public function findCompanyProducts(Request $request, ApiResponse $apiResponse): Response
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
        $company = $this->em->getRepository(Company::class)->findOneActive($companyId);
        $products = $this->em->getRepository(Product::class)->findAllActiveProductsInCompanyOrderByCategory($companyId);
        $productsCategories = $this->em->getRepository(ProductCategory::class)->findByCompany($companyId);
        $address = $this->em->getRepository(CompanyAddress::class)->findByCompany($companyId);

        $data = [
            "company" => $company,
            "address" => $address,
            "productsCategories" => $productsCategories,
            "products" => $products
        ];

        return $this->json($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/companies/products/category', methods: ["GET"])]
    public function findCompanyProductsByCategory(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "company_id" => [
                "type" => "integer"
            ],
            "category_id" => [
                "type" => "integer"
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isParamsExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $companyId = $response["company_id"];
        $categoryId = $response["category_id"];
        $products = $this->em->getRepository(Product::class)->findAllActiveProductsByCompanyByCategory($companyId, $categoryId);

        return $this->json($products, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/companies/products/best', methods: ["GET"], name: 'app_company_products_top_5')]
    public function findCompanyBestProducts(Request $request, ApiResponse $apiResponse): Response
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
        $products = $this->em->getRepository(Product::class)->findMostSoldProductsForOneCompany($companyId);

        return $this->json($products, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/companies/orders', methods: ["GET"], name: 'app_orders_company')]
    public function findOrdersCompany(Request $request, ApiResponse $apiResponse): Response
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
        $products = $this->em->getRepository(Order::class)->findOrdersByCompany($companyId);

        return $this->json($products, 200, ['Content-Type' => 'application/json']);
    } 
    
    #[Route('/companies', methods: ["POST"])]
    public function addCompany(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "name" => [
                "type" => "string",
            ],
            "image" => [
                "type" => "string",
            ],
            "company_category_id" => [
                "type" => "integer",
            ],
            "owner_id" => [
                "type" => "integer",
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $company = new Company();

        $name = $response["name"];
        $company->setName($name);

        $image = $response["image"];
        $company->setImage($image);

        $companyCategoryId = $response["company_category_id"];
        $companyCategory = $this->em->getRepository(CompanyCategory::class)->find($companyCategoryId);
        $company->setCompanyCategoryId($companyCategory);

        $ownerId = $response["owner_id"];
        $owner = $this->em->getRepository(User::class)->find($ownerId);
        $company->setOwner($owner);

        $company->setCreatedAt(new DateTimeImmutable());
        $company->setUpdatedAt(new DateTimeImmutable());

        $this->em->persist($company);
        $this->em->flush();
        return $this->json(true);
    }

    #[Route('/companies/{id}', methods: ["POST"])]
    public function updateCompany(int $id, Request $request, ApiResponse $apiResponse): Response
    {
        $company = $this->em->getRepository(Company::class)->find($id);

        if (empty($company)) {
            return $this->json("Doesn't exist", 404);
        }
        
        $params = [
            "name" => [
                "type" => "string",
                "required" => false
            ],
            "image" => [
                "type" => "string",
                "required" => false
            ],
            "company_category_id" => [
                "type" => "integer",
                "required" => false
            ],
            "owner_id" => [
                "type" => "integer",
                "required" => false
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }

        $name = $response["name"] ?? null;
        if ($name) {
            $company->setName($name);
        }

        $image = $response["image"] ?? null;
        if ($image) {
            $company->setImage($image);
        }

        $companyCategoryId = $response["company_category_id"] ?? null;
        if ($companyCategoryId) {
            $companyCategory = $this->em->getRepository(CompanyCategory::class)->find($companyCategoryId);
            $company->setCompanyCategoryId($companyCategory);
        } 

        $ownerId = $response["owner_id"] ?? null;
        if ($ownerId) {
            $owner = $this->em->getRepository(User::class)->find($ownerId);
            $company->setOwner($owner);
        } 

        $company->setUpdatedAt(New DateTimeImmutable());

        $this->em->persist($companyCategory);
        $this->em->flush();
        return $this->json(true);
    }
}
