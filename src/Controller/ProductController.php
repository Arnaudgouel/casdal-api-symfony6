<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

    #[Route('/products', methods: ["GET"], name: 'app_products')]
    public function findCompanies(Request $request): Response
    {
        $companyId = intval($request->query->get("company_id"));
        try {
            if (!$companyId) {
                throw new Exception("Missing get parameter : company_id - The value has to be an integer", 400);
            }
            if ($companyId != $request->query->get("company_id")) {
                throw new Exception("Only digits are accepted for category_id parameter", 400);
            }
            $products = $this->em->getRepository(Product::class)->findAllActiveProductsInCompanyOrderByCategory($companyId);
            if (empty($products)) {
                throw new Exception("No products found in this company", 204);
            }
        } catch (Exception $e) {
            return $this->json($e->getMessage(), $e->getCode());
        }

        return $this->json($products, 200, ['Content-Type' => 'application/json']);
    }
}
