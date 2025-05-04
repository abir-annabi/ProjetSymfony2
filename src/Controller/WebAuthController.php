<?php
// src/Controller/WebAuthController.php
// src/Controller/WebAuthController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class WebAuthController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $lastUsername = $authenticationUtils->getLastUsername() ?? '';
        
        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Encodez le mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
    
            $entityManager->persist($user);
            $entityManager->flush();
            
            // Envoi de l'email de confirmation
            $email = (new Email())
                ->from('no-reply@kteby.com')
                ->to($user->getEmail())
                ->subject('Bienvenue sur Kteby !')
                ->html($this->renderView(
                    'emails/registration.html.twig',
                    ['user' => $user]
                ));
            
            $mailer->send($email);
    
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method will be intercepted by the logout key on your firewall.');
    }
}