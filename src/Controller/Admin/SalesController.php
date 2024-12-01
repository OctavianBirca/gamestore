<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Sale;
use Symfony\Component\HttpFoundation\Request;

class SalesController extends AbstractController
{
    private Sale $salesService;

    public function __construct(Sale $salesService)
    {
        $this->salesService = $salesService;
    }

    #[Route('/sales', name: 'sales_overview')]
    public function index(Request $request): Response
    {
        // Set default dates
        $currentMonthStart = date('Y-m-01');
        $currentDate = date('Y-m-d');
        $startDate = $request->query->get('startDate', $currentMonthStart);
        $endDate = $request->query->get('endDate', $currentDate);

        // Get sales data and store summary
        $salesData = $this->salesService->getSalesByDateRange($startDate, $endDate);
        $storeSummary = $this->salesService->getStoreSummary();

        return $this->render('admin/sales/index.html.twig', [
            'sales' => $salesData,
            'storeSummary' => $storeSummary,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}