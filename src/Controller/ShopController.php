<?php

namespace App\Controller;


use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    #[Route('/magasin/{slug}', name: 'app_shop')]
    public function index($slug, ShopRepository $shopRepository): Response
    {   
        
        $shop = $shopRepository->findOneBySlug($slug);


        return $this->render('shop/index.html.twig', [
            'shop' => $shop,
        ]);
    }
}

