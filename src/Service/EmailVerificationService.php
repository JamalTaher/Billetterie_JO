<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Uid\Uuid;
use Twig\Environment;
use Psr\Log\LoggerInterface; 

class EmailVerificationService
{
    private $entityManager;
    private $mailer;
    private $urlGenerator;
    private $tokenStorage;
    private $authorizationChecker;
    private $twig;
    private LoggerInterface $logger; 
    private $tokenLifetime = 900;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, Environment $twig, LoggerInterface $logger) // Modifiez le constructeur
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->twig = $twig;
        $this->logger = $logger; 
    }

    public function generateVerificationCode(Utilisateur $user): string
    {
        return Uuid::v4()->toBase58();
    }

    public function sendVerificationEmail(Utilisateur $user, string $verificationCode): void
    {
        $user->setEmailVerificationCode($verificationCode);
        $user->setEmailVerificationRequestedAt(new \DateTimeImmutable());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $verificationUrl = $this->urlGenerator->generate('app_verify_email', ['code' => $verificationCode, 'email' => $user->getEmail()], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new Email())
            ->from('no-reply@appjo2024.com') 
            ->to($user->getEmail())
            ->subject('Confirmez votre adresse email')
            ->html($this->twig->render('security/verify_email_content.html.twig', [
                'verificationUrl' => $verificationUrl,
                'tokenLifetime' => $this->tokenLifetime / 60,
            ]))
        ;

        try {
            $this->mailer->send($email);
            $this->logger->info(sprintf('E-mail de vérification renvoyé à %s', $user->getEmail()));
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Erreur lors de l\'envoi de l\'e-mail de vérification à %s: %s', $user->getEmail(), $e->getMessage()));
        }
    }

    public function isVerificationCodeValid(Utilisateur $user, string $code): bool
    {
        if (null === $user->getEmailVerificationCode() || null === $user->getEmailVerificationRequestedAt()) {
            return false;
        }

        if (!hash_equals($user->getEmailVerificationCode(), $code)) {
            return false;
        }

        if ($user->getEmailVerificationRequestedAt()->getTimestamp() + $this->tokenLifetime < time()) {
            return false;
        }

        return true;
    }

    public function markEmailAsVerified(Utilisateur $user): void
    {
        $user->setEmailVerificationCode(null);
        $user->setEmailVerificationRequestedAt(null);

        $user->setIsVerified(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function resendVerificationEmail(Utilisateur $user): void
    {
        $verificationCode = $this->generateVerificationCode($user);
        $this->sendVerificationEmail($user, $verificationCode);
    }
}