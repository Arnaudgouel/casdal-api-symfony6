<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/company', name: 'app_company')]
    public function index(): Response
    {
        $companies = $this->em->getRepository(Company::class)->findAllActive();
        return $this->json($companies);
        return $this->render('company/index.html.twig', [
            'controller_name' => 'CompanyController',
        ]);
    }
}
