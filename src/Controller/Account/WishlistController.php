<?php

namespace App\Controller\Account;


use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WishlistController extends AbstractController
{
    #[Route('/account/wishlist', name: 'app_account_wishlist')]
    public function index(): Response
    {
        return $this->render('account/wishlist/index.html.twig');
    } 
    
    #[Route('/account/wishlist/add/{id}', name: 'app_account_wishlist_add')]
    public function add(ProductRepository $productRepository, EntityManagerInterface $entityManager, Request $request, $id): Response
    {
        $product = $productRepository->findOneById($id);
        if($product) {
            $this->getUser()->addWishlist($product);
            $entityManager->flush();

        }
  $this->addFlash(
            'success',
            "Le produit a ete ajoute a vortre liste de souhait");
        

        return $this->redirect($request->headers->get('referer'));

    }

    #[Route('/account/wishlist/remove/{id}', name: 'app_account_wishlist_remove')]
    public function remove(ProductRepository $productRepository, EntityManagerInterface $entityManager, Request $request,  $id): Response
    {
        $product = $productRepository->findOneById($id);
        if($product) {
            $this->addFlash('success','Le produit a ete supprime');

            $this->getUser()->removeWishlist($product);
            $entityManager->flush();
        } else {
            $this->addFlash('warning','Le produit n\'a ete trouve ');

        }

        return $this->redirect($request->headers->get('referer'));


    }

}
