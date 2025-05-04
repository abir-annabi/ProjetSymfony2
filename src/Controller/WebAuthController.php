<?php
// src/Controller/WebAuthController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class WebAuthController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
public function login(AuthenticationUtils $authenticationUtils): Response
{
    // Debug: Vérifiez les valeurs reçues
    $lastUsername = $authenticationUtils->getLastUsername();
    if (null === $lastUsername) {
        $lastUsername = ''; // Valeur par défaut
    }

    return $this->render('auth/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $authenticationUtils->getLastAuthenticationError()
    ]);
}

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                // Affichez toutes les erreurs de validation
                $errors = $form->getErrors(true);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->render('auth/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }
    
            // Encodez le mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            $this->addFlash('success', 'Inscription réussie !');
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