<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AllProductsController extends AbstractController
{
    #[Route('/tous-les-jeux', name: 'app_all_products')]
    public function index(ProductRepository $productRepository): Response
    {
        $allProducts = $productRepository->findAll();

        return $this->render('all_products/index.html.twig', [
            'allProducts' => $allProducts,
        ]);
    }
}
