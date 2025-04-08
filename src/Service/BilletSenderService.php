<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class BilletSenderService
{
    private $mailer;
    private $twig;
    private string $billetSenderEmail = 'inscription@billetterie-jo.com'; 
    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
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
        } catch (\Throwable $e) {
            
            dump('Erreur lors de l\'envoi de l\'e-mail du billet : ' . $e->getMessage()); 
        }
    }
}