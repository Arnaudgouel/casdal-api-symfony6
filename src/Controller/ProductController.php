<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ApiResponse;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/companies/products', methods: ["POST"])]
    public function addCompanyProducts(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "name" => [
                "type" => "string",
            ],
            "image" => [
                "type" => "string",
                "required" => false
            ],
            "description" => [
                "type" => "string",
                "required" => false
            ],
            "product_category_id" => [
                "type" => "integer",
            ],
            "price" => [
                "type" => "integer",
            ],
            "available" => [
                "type" => "bool",
            ],
            "company_id" => [
                "type" => "integer",
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $product = new Product();

        $productCategory = $this->em->getRepository(ProductCategory::class)->find($response["product_category_id"]);
        $product->setProductCategoryId($productCategory);
        $company = $this->em->getRepository(Company::class)->find($response["company_id"]);
        $product->setCompanyId($company);
        
        $product->setName($response["name"]);
        
        $image = $response["image"] ?? null;
        if ($image) $product->setImage($image);

        $product->setDescription($response["description"]);
        $product->setPrice($response["price"]);
        $product->setAvailable($response["available"]);

        $product->setCreatedAt(new DateTimeImmutable());
        $product->setUpdatedAt(new DateTimeImmutable());

        $this->em->persist($product);
        $this->em->flush();
        return $this->json(true);
    }

    #[Route('/companies/products/{id}', methods: ["POST"])]
    public function updateCompanyProducts(int $id, Request $request, ApiResponse $apiResponse): Response
    {
        $product = $this->em->getRepository(Product::class)->find($id);

        if (empty($product)) {
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
            "description" => [
                "type" => "string",
                "required" => false
            ],
            "product_category_id" => [
                "type" => "integer",
                "required" => false
            ],
            "price" => [
                "type" => "integer",
                "required" => false
            ],
            "available" => [
                "type" => "bool",
                "required" => false
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }

        $name = $response["name"] ?? null;
        if ($name) $product->setName($name);
        
        $image = $response["image"] ?? null;
        if ($image) $product->setImage($image);

        $description = $response["description"] ?? null;
        if ($description) $product->setDescription($description);

        $productCategoryId = $response["product_category_id"] ?? null;
        if ($productCategoryId) { 
            $productCategory = $this->em->getRepository(ProductCategory::class)->find($productCategoryId);
            $product->setProductCategoryId($productCategory);
        }
        
        $price = $response["price"] ?? null;
        if ($price) $product->setPrice($price);

        $available = $response["available"] ?? null;
        if ($available) $product->setAvailable($available);

        $product->setUpdatedAt(New DateTimeImmutable());
        
        $this->em->persist($product);
        $this->em->flush();
        return $this->json(true);
    }
}
