<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Classe\SalesService;

$salesService = new SalesService();
$salesService->recordSale("Test Product", 99.99, date('d-m-Y H:i:s'), "Test Store", 1);
