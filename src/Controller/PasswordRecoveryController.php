<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\PasswordRecoveryFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordRecoveryController extends AbstractController
{   

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;        
    }

    #[Route('/password-recovery', name: 'app_password')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
       
        $form = $this->createForm(PasswordRecoveryFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneByEmail($email);
            $this->addFlash('success', "Votre email de reinitialisation a ete transmis");
            
            if ($user) {

                //creating a token for the passord reset
                $randomBytes = random_bytes(15);
                $token = bin2hex($randomBytes);
                $user->setToken($token);

                $date = new DateTime();
                $date->modify('+10 minutes');
                
                
                $user->setTokenExpireAt($date);
                
                $this->em->flush();

                                    

                $mail = new Mail ();
                
                $vars = [
                    'link' => $this->generateUrl('app_password_reset', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
                ];
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), "Modification de votre mot de passe", "passwordrecovery.html", $vars );
                
            }
        
        }
        
    
        return $this->render('password/index.html.twig', [
            'PasswordRecoveryFormType' => $form->createView(),
        ]);
    }

    #[Route('/password/reset/{token}', name: 'app_password_reset')]
    public function reset(Request $request, UserRepository $userRepository, $token): Response
    {
        if (!$token) {
            dd('token does not exist');
            return $this->redirectToRoute('app_password');
        } 
        
        $now = new DateTime();

        $user = $userRepository->findOneByToken($token);

        

        if (!$user || $user->getTokenExpireAt() < $now) {
            dd('token expired ');
            return $this->redirectToRoute('app_password');
        } 
        
        $form = $this->createForm(ResetPasswordFormType::class, $user );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken(null);
            $user->setTokenExpireAt(null);
            $this->em->flush();
            $this->addFlash(
                'success',
                "Votre mot de passe a ete modifier!"
            );

            return $this->redirectToRoute('app_login');
        
        }


        return $this->render('password/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }




}
