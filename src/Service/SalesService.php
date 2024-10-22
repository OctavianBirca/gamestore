<?php

namespace App\Service;

use MongoDB\Client;

class SalesService
{
    private $mongoClient;

    public function __construct()
    {
        
        $mongodbUrl = $_ENV['MONGODB_URL'] ?? null;

        if (!$mongodbUrl) {
            throw new \RuntimeException('The MongoDB URL is not set in the environment variables.');
        }

        $this->mongoClient = new Client($mongodbUrl);
    }

    public function addSale(string $productName, float $price, string $store, \DateTime $date): void
    {
        $collection = $this->mongoClient->gamestore->sales;
        $collection->insertOne([
            'productName' => $productName,
            'price' => $price,
            'store' => $store,
            'date' => $date->format('Y-m-d H:i:s'),
        ]);
    }

    public function getSales(array $filters = []): array
    {
        $collection = $this->mongoClient->gamestore->sales;
        return $collection->find($filters)->toArray();
    }
}
