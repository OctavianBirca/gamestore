<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
     
        // Creating a new user
        $user = new User();

        // Creating the registration form
        $form = $this->createForm(RegisterUserType::class, $user);

        // Handling the request, mapping the form to the user object
        $form->handleRequest($request);

        // Checking if the form is submitted and valid with Symfony's built-in methods 

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Votre a ete cree!"
            );

            // Sending a confirmation email
            $mail = new Mail ();
            $vars = [
                'firstname' => $user->getFirstname()
            ];
            $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), "Votre mail a ete bien cree", "welcome.html", $vars );

            return $this->redirectToRoute('app_login');
        }
     
        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }
}
