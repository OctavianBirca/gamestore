<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Service\Sale;

$salesService = new Sale();
$salesService->recordSale("Test Product", 99.99, date('d-m-Y H:i:s'), "Test Store", 1);
