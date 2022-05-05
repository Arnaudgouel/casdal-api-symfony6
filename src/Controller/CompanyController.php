<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\CompanyAddress;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Service\ApiResponse;
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
    public function findCompanies(Request $request): Response
    {
        $search = $request->query->get("search");

        if ($search) {
            $companies = $this->em->getRepository(Company::class)->findAllActiveWithSearch($search);
        } else {
            $companies = $this->em->getRepository(Company::class)->findAllActive();
        }

        return $this->json($companies, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/companies/category', methods: ["GET"], name: 'app_company_category')]
    public function allInOneCategory(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "category_id" => "integer"
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
            "company_id" => "integer"
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isParamsExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $companyId = $response["company_id"];
        $products = $this->em->getRepository(Product::class)->findAllActiveProductsInCompanyOrderByCategory($companyId);
        $productsCategories = $this->em->getRepository(ProductCategory::class)->findByCompany($companyId);
        $address = $this->em->getRepository(CompanyAddress::class)->findByCompany($companyId);

        $data = [
            "address" => $address,
            "productsCategories" => $productsCategories,
            "products" => $products
        ];

        return $this->json($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/companies/products/best', methods: ["GET"], name: 'app_company_products_top_5')]
    public function findCompanyBestProducts(Request $request, ApiResponse $apiResponse): Response
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
        $products = $this->em->getRepository(Product::class)->findMostSoldProductsForOneCompany($companyId);

        return $this->json($products, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/companies/orders', methods: ["GET"], name: 'app_orders_company')]
    public function findOrdersCompany(Request $request, ApiResponse $apiResponse): Response
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
