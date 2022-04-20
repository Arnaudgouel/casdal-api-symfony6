<?php

namespace App\Controller;

use App\Entity\Company;
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

    #[Route('/companies/category/{id}', methods: ["GET"], name: 'app_company_category')]
    public function allInOneCategory(Request $request): Response
    {
        $categoryId = intval($request->query->get("category_id"));
        try {
            if ($categoryId != $request->query->get("category_id")) {
                throw new Exception("Only digits are accepted for category_id parameter");
            }
            $companies = $this->em->getRepository(Company::class)->findAllActiveInCategory($categoryId);
            if (empty($companies)) {
                throw new Exception("No companies found in this category");
            }
        } catch (Exception $e) {
            return $this->json($e->getMessage());
        }
        return $this->json($companies);
    }

    #[Route('/companies/owned/{id}', methods: ["GET"], name: 'app_company_owned_by_user')]
    public function allCompaniesManagedByUser(int $id, Request $request): Response
    {
        $request->query->get("page");
        $companies = $this->em->getRepository(Company::class)->findAllActiveCompaniesManagedByUser($id);
        return $this->json($companies);
    }
}
