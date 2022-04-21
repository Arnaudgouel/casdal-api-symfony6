<?php

namespace App\Controller;

use App\Entity\CompanyCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
