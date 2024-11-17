<?php

namespace App\Twig;

use App\Classe\Cart;
use App\Entity\Shop;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtentions extends AbstractExtension implements GlobalsInterface
{   
    private $categoryRepository;
    private $productRepository;
    private $shopRepository;
    private $cart;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, Cart $cart, ShopRepository $shopRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->shopRepository = $shopRepository;
    }

    public function getFilters()
    {

        return [
            new TwigFilter('priceEuro', [$this, 'formatPrice'])

        ];
    }

    public function formatPrice($number)
    {
        return number_format($number, '2',','). ' €';
    }

    public function getGlobals(): array
    {
        return [ 
            'allCategories' => $this->categoryRepository->findAll(),
           //'allProducts' => $this->productRepository->findAll(),
            'allStores' => $this->shopRepository->findAll(),
            'fullCartQuantity' => $this->cart->fullQuantity()
        ];

    }
}