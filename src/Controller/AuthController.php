<?php
// src/Controller/AuthController.php
// src/Controller/AuthController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class AuthController extends AbstractController
{
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        throw $this->createNotFoundException('Use /login for web authentication');
    }

    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setBirthDate(new \DateTime($data['birthDate']));
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
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

        return $this->json([
            'message' => 'User registered successfully. A confirmation email has been sent.',
            'user' => [
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName()
            ]
        ], 201);
    }
}