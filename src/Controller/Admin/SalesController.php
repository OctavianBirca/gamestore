<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Classe\SalesService;

class SalesController extends AbstractController
{
    private SalesService $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    #[Route('/sales', name: 'sales_overview')]
    public function index(): Response
    {
        // Retrieve sales data from MongoDB
        $salesData = $this->salesService->getAllSales();

        return $this->render('Sales/index.html.twig', [
            'sales' => $salesData
        ]);
    }
}
