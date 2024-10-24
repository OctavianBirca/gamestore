<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class InvoiceController extends AbstractController
{
    #[Route('/account/invoice/print/{id_order}', name: 'app_invoice')]
    public function printForCostumer(OrderRepository $orderRepository, $id_order): Response
    {
        $order = $orderRepository->findOneById($id_order);

        if(!$order) {
            return $this->redirectToRoute('app_account');
        }

        if ($order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account');
        }

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();

        $html = $this->renderView('invoice/index.html.twig',[
            'order' => $order    
        ]);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('facture.pdf', [
            'Attachment' => false
        ]);

        exit ();
    }

    #[Route('/admin/invoice/print/{id_order}', name: 'app_invoice_admin')]
    public function printForAdmin(OrderRepository $orderRepository, $id_order): Response
    {
        $order = $orderRepository->findOneById($id_order);

        if(!$order) {
            return $this->redirectToRoute('app_account');
        }

       
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();

        $html = $this->renderView('invoice/index.html.twig',[
            'order' => $order    
        ]);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('facture.pdf', [
            'Attachment' => false
        ]);

        exit ();
    }

}
