<?php

namespace App\Controller;

use App\Entity\CompanyCategory;
use App\Service\ApiResponse;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyCategoryController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/company-categories', methods: ["GET"], name: 'app_company_categories')]
    public function findCompanies(): Response
    {
        $companyCategories = $this->em->getRepository(CompanyCategory::class)->findAllActive();

        return $this->json($companyCategories, 200, ['Content-Type' => 'application/json']);
    }
    #[Route('/company-categories', methods: ["POST"])]
    public function addCompanyCategory(Request $request, ApiResponse $apiResponse): Response
    {
        $params = [
            "title" => [
                "type" => "string",
            ],
            "image" => [
                "type" => "string",
                "required" => false
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }
        $companyCategory = new CompanyCategory();

        $title = $response["title"];
        $companyCategory->setTitle($title);

        $image = $response["image"] ?? null;
        if ($image) $companyCategory->setImage($image);

        $companyCategory->setCreatedAt(new DateTimeImmutable());
        $companyCategory->setUpdatedAt(new DateTimeImmutable());

        $this->em->persist($companyCategory);
        $this->em->flush();
        return $this->json(true);
    }

    #[Route('/company-categories/{id}', methods: ["POST"])]
    public function updateCompanyCategories(int $id, Request $request, ApiResponse $apiResponse): Response
    {
        $companyCategory = $this->em->getRepository(CompanyCategory::class)->find($id);

        if (empty($companyCategory)) {
            return $this->json("Doesn't exist", 404);
        }
        
        $params = [
            "title" => [
                "type" => "string",
                "required" => false
            ],
            "image" => [
                "type" => "string",
                "required" => false
            ],
        ];
        $apiResponse->setParams($params);
        $response = $apiResponse->isBodyExistAndCorrectType($request);
        if ($apiResponse->hasError) {
            return $this->json($response, 400, ['Content-Type' => 'application/json']);
        }

        $title = $response["title"] ?? null;
        if ($title) $companyCategory->setTitle($title);

        $image = $response["image"] ?? null;
        if ($image) $companyCategory->setImage($image);

        $companyCategory->setUpdatedAt(New DateTimeImmutable());

        $this->em->persist($companyCategory);
        $this->em->flush();
        return $this->json(true);
    }
}
