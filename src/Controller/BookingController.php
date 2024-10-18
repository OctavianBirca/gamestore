<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\BookingType;
use App\Repository\OrderRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class BookingController extends AbstractController
{
    #[Route('/order/pickup', name: 'app_booking')]
    public function booking(): Response
    {   
        $addresses = $this->getUser()->getAddresses();
        

        if(count($addresses)==0) {
            return $this->redirectToRoute('app_account_address_form');

        }
        $form = $this->createForm(BookingType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_booking_summary')
        ]);

        return $this->render('order/booking.html.twig', [
            'bookingForm' => $form->createView(),
        ]);
    }



    #[Route('/order/summary-pickup', name: 'app_booking_summary')]
    public function book(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {   
        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }

        $products = $cart->getCart();

        $form = $this->createForm(BookingType::class, null, [
            'addresses' => $this->getUser()->getAddresses(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $pickupDate = $form->get('pickup_date')->getData();
            $currentDate = new DateTime();
            $maxDate = (clone $currentDate)->modify('+7 days');


            if ($pickupDate < $currentDate || $pickupDate > $maxDate || in_array($pickupDate->format('l'), ['Monday', 'Sunday'])) {
                $this->addFlash('error', 'La date de retrait est invalide. Veuillez choisir une date valide.');
                return $this->redirectToRoute('app_reservation');
            }

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

            $entityManager->persist($order);
            $entityManager->flush();
            

        return $this->render('order/summary-booking.html.twig', [
            'choices' => $form->getData(),
            'cart' => $products,
            'order' => $order,
            'totalSum' => $cart->getTotalSum(),
        ]);
        }
    }

    #[Route('/order/reserve/{id_order}', name: 'app_booking_reserve')]
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
            return $this->redirectToRoute('app_booking');
        }

        $order->setState(6); 
        $entityManager->flush();

        if ($order->getState() == 6){
            $cart->remove();
            $entityManager->flush();
        }
              

        // Redirect to a success page
        return $this->redirectToRoute('app_booking_success', ['id_order' => $order->getId()]);
    }

    #[Route('/order/success/{id_order}', name: 'app_booking_success')]
    public function reservationSuccess(int $id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManager, Cart $cart): Response
    {
        // Retrieve the order by its ID
        $order = $orderRepository->find($id_order);

        // Make sure the order exists and belongs to the current user
        if (!$order || $order->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Réservation introuvable.');
            return $this->redirectToRoute('app_booking');
        }

        if ($order->getState() == 6){
            
            $cart->remove();
            $entityManager->flush();
        }
        

               

        return $this->render('booking/success.html.twig', [
            'order' => $order,
        ]);
    }
    

}               
