<?php

namespace App\Controller;

use App\Service\Cart;
use App\Service\Mail;
use App\Service\State;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\BookingType;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



class OrderController extends AbstractController
{
    #[Route('/order/reservation', name: 'app_order')]
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
            'orderForm' => $form->createView(),
        ]);
    }


    #[Route('/order/summary', name: 'app_order_summary')]
    public function book(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {   
        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }
    
        $products = $cart->getCart();
    
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses(),
        ]);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            
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
            $order->setShop($form->get('shop')->getData());
            $order->setPickupDate($form->get('pickup_date')->getData());
            $order->setDelivery($address);
    
            foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductImage($product['object']->getImage());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetail);
            }

            $order->setTotalPrice($cart->getTotalSum());
    
            $entityManager->persist($order);
            $entityManager->flush();
    
            return $this->render('order/summary.html.twig', [
                'choices' => $form->getData(),
                'cart' => $products,
                'order' => $order,
                'totalSum' => $cart->getTotalSum(),
            ]);
        }
    
        
        $this->addFlash('error', 'Le formulaire n\'est pas valide.');
        return $this->redirectToRoute('app_order');
    }
    
    
    #[Route('/order/reservation/{id_order}', name: 'app_order_booking')]
    public function confirmReservation(int $id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManager, Cart $cart): Response
    {
        // Retrieve the order by its ID
        $order = $orderRepository->findOneById($id_order);


        $order = $orderRepository->findOneBy([
            'id' => $id_order,
            'user' => $this->getUser()
        ]);


        // Make sure the order exists and belongs to the current user
        if (!$order || $order->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Réservation introuvable.');
            return $this->redirectToRoute('app_order');
        }

        $order->setState(6); 
        $entityManager->flush();

        if ($order->getState() == 6){
            $cart->remove();
            $entityManager->flush();
        }
              

        // Redirect to a success page
        return $this->redirectToRoute('app_order_success', ['id_order' => $order->getId()]);
    }

    
    
    #[Route('/order/success/{id_order}', name: 'app_order_success')]
    public function reservationSuccess(int $id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManager, Cart $cart): Response
    {
        // Retrieve the order by its ID
        $order = $orderRepository->find($id_order);

        // Make sure the order exists and belongs to the current user
        if (!$order || $order->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Réservation introuvable.');
            return $this->redirectToRoute('app_order');
        }

        if ($order->getState() == 6){
            
            $state = $order->getState();
            $cart->remove();
            $entityManager->flush();

            $mail = new Mail();
            $vars = [
                'firstname' => $order->getUser()->getFirstname(),
                'id_order' => $order->getId()
            ];
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname().' '.$order->getUser()->getLastname(), State::STATE[$state]['email_subject'], State::STATE[$state]['email_template'], $vars);
        
            return $this->redirectToRoute('app_order_success', ['id_order' => $order->getId()]);


        }
                       
        return $this->render('order/success.html.twig', [
            'order' => $order,
        ]);
    }
    

    

}
