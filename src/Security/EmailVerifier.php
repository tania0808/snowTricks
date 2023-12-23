<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EmailVerifier
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $router
    ) {
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, UserInterface $user, TemplatedEmail $email, JWTTokenManagerInterface $tokenManager): void
    {
        $token = $tokenManager->create($user);
        $validationUrl = $this->router->generate($verifyEmailRouteName, ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = $email->getContext();
        $context['username'] = $user->getUserName();
        $context['signedUrl'] = $validationUrl;

        $email->context($context);

        $this->mailer->send($email);
    }

    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}