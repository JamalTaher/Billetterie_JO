<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Psr\Log\LoggerInterface;

class BilletSenderService
{
    private $mailer;
    private $twig;
    private LoggerInterface $logger;
    private string $billetSenderEmail = 'no-reply@appjo2024.com'; 

    public function __construct(MailerInterface $mailer, Environment $twig, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function envoyerBilletParEmail(Utilisateur $utilisateur, array $detailsBillet, ?string $cheminQRCode = null): void
    {
        $email = (new Email())
            ->from($this->billetSenderEmail)
            ->to($utilisateur->getEmail())
            ->subject('Votre billet pour ' . $detailsBillet['nom_evenement'])
            ->html($this->twig->render('emails/billet_achat.html.twig', [
                'details_billet' => $detailsBillet,
                'utilisateur' => $utilisateur,
            ]));

        if ($cheminQRCode) {
            $email->attachFromPath($cheminQRCode, 'qrcode_' . $detailsBillet['reference_billet'] . '.png');
        }

        try {
            $this->mailer->send($email);
            $this->logger->info(sprintf('Billet envoyé par e-mail à %s pour l\'événement %s (référence: %s)', $utilisateur->getEmail(), $detailsBillet['nom_evenement'], $detailsBillet['reference_billet']));
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Erreur lors de l\'envoi de l\'e-mail du billet à %s pour l\'événement %s (référence: %s): %s', $utilisateur->getEmail(), $detailsBillet['nom_evenement'], $detailsBillet['reference_billet'], $e->getMessage()));
        }
    }
}