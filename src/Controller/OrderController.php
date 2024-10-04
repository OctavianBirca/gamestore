<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class OrderController extends AbstractController
{
    #[Route('/order/delivery', name: 'app_order')]
    public function index(): Response
    {   
        $addresses = $this->getUser()->getAddresses();
        

        if(count($addresses)==0) {
            return $this->redirectToRoute('app_account_address_form');

        }
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary')
        ]);

        return $this->render('order/index.html.twig', [
            'deliveryForm' => $form->createView(),
        ]);
    }



    #[Route('/order/summary', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {   
        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }

        $products = $cart->getCart();

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $addressObj = $form->get('addresses')->getData(); 

            $address = $addressObj->getFirstname().' '.$addressObj->getLastname(). '<br/>';
            $address .= $addressObj->getAddress(). '<br/>';
            $address .= $addressObj->getPostal().' '.$addressObj->getCity(). '<br/>';
            $address .= $addressObj->getCountry(). '<br/>';
            $address .= $addressObj->getPhone();

           
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new DateTime());
            $order->setState(1);
            $order->setCarrier($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);

            foreach ($products as $product) {
                
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductImage($product['object']->getImage());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetail);
            }

            $entityManager->persist($order);
            $entityManager->flush();


        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $products,
            'order' => $order,
            'totalSum' => $cart->getTotalSum(),
        ]);
        
        }
    }
}
