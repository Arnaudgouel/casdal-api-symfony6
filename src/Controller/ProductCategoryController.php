<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Service\ApiResponse;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductCategoryController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/product-categories', methods: ["POST"])]
    public function addProductCategory(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "name" => [
                "type" => "string",
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $productCategory = new ProductCategory();

        $name = $response["name"];
        $productCategory->setName($name);

        $productCategory->setCreatedAt(new DateTimeImmutable());
        $productCategory->setUpdatedAt(new DateTimeImmutable());

        $this->em->persist($productCategory);
        $this->em->flush();
        return $this->json(true);
    }

    #[Route('/product-categories/{id}', methods: ["POST"])]
    public function updateProductCategories(int $id, Request $request, ApiResponse $apiResponse): Response
    {
        $productCategory = $this->em->getRepository(ProductCategory::class)->find($id);

        if (empty($productCategory)) {
            return $this->json("Doesn't exist", 404);
        }
        
        $params = [
            "name" => [
                "type" => "string",
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }

        $name = $response["name"];
        $productCategory->setName($name);

        $productCategory->setUpdatedAt(New DateTimeImmutable());

        $this->em->persist($productCategory);
        $this->em->flush();
        return $this->json(true);
    }
}
