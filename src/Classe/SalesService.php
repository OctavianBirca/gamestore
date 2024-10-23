<?php



namespace App\Classe;
require_once __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\Collection;

class SalesService
{
    private Collection $collection;

    public function __construct()
    {
        $mongoUrl = "mongodb://127.0.0.1:27017/gamestore-mdb";
        

        
       
        $client = new Client($mongoUrl);
        $database = $client->selectDatabase('store');
        $this->collection = $database->selectCollection('sales');
    }

   
    public function recordSale(string $productName, float $price, string $saleDate, string $store, int $quantity): void
    {
        $saleData = [
            'productName' => $productName,
            'price' => $price,
            'saleDate' => $saleDate,
            'store' => $store,
            'quantity' => $quantity
        ];

        
        

        try {
            $this->collection->insertOne($saleData);
            dump("Successfully inserted sale to MongoDB");
        } catch (\Exception $e) {
            dump('Error inserting sale into MongoDB: ' . $e->getMessage());
        }
    }


    public function getAllSales(): array
    {
        // Fetch all sales records from MongoDB
        return $this->collection->find()->toArray();
    }



}
