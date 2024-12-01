<?php

namespace App\Service;

use MongoDB\Client;
use MongoDB\Collection;

class Sale
{
    private Collection $collection;

    public function __construct()
    {
        // Timeout for execution
        ini_set('max_execution_time', 300);
        
        // MongoDB connection options
        $options = [
            'connectTimeoutMS' => 60000,
            'socketTimeoutMS' => 60000,
            'serverSelectionTimeoutMS' => 60000
        ];

        $mongoUrl = "mongodb://127.0.0.1:27017";
        $client = new Client($mongoUrl, $options);
        $database = $client->selectDatabase('store');
        $this->collection = $database->selectCollection('sales');
    }

    public function getSalesByDateRange(?string $startDate = null, ?string $endDate = null): array
    {
        $filter = [];
        if ($startDate && $endDate) {
            $filter = [
                'saleDate' => [
                    '$gte' => $startDate,
                    '$lte' => $endDate
                ]
            ];
        }

        $options = [
            'sort' => ['saleDate' => 1], 
            'limit' => 1000,
            'projection' => [
                'productName' => 1,
                'price' => 1,
                'saleDate' => 1,
                'store' => 1,
                'quantity' => 1,
                '_id' => 1
            ]
        ];

        return $this->collection->find($filter, $options)->toArray();
    }

    public function getStoreSummary(): array
    {
        $pipeline = [
            [
                '$group' => [
                    '_id' => '$store',
                    'totalSales' => [
                        '$sum' => [
                            '$multiply' => ['$price', '$quantity']
                        ]
                    ],
                    'totalQuantity' => ['$sum' => '$quantity']
                ]
            ],
            [
                '$sort' => ['totalSales' => -1]
            ],
            [
                '$limit' => 100
            ]
        ];

        $options = ['maxTimeMS' => 60000];
        
        return $this->collection->aggregate($pipeline, $options)->toArray();
    }

    public function recordSale(string $productName, float $productPrice, string $saleDate, string $storeName, int $quantity): void
    {
        $saleData = [
            'productName' => $productName,
            'price' => $productPrice,
            'saleDate' => $saleDate,
            'store' => $storeName,
            'quantity' => $quantity,
        ];

        $this->collection->insertOne($saleData);
    }
}