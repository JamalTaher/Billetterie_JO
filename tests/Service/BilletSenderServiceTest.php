<?php

namespace App\Tests\Service;

use App\Service\BilletSenderService;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface; 
use Symfony\Component\Mime\Email; 
use Twig\Environment as TwigEnvironment; 
use Psr\Log\LoggerInterface; 
use Symfony\Component\Mime\Part\DataPart; 

class BilletSenderServiceTest extends KernelTestCase
{
    private BilletSenderService $billetSenderService;
    private $mailerMock;
    private $twigMock;
    private $loggerMock;

    protected function setUp(): void
    {
        
        $this->mailerMock = $this->createMock(MailerInterface::class);
        $this->twigMock = $this->createMock(TwigEnvironment::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->billetSenderService = new BilletSenderService(
            $this->mailerMock,
            $this->twigMock,
            $this->loggerMock
        );
    }

    private function createMockUtilisateur(): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('billet_user@example.com');
        $utilisateur->setNom('Billet');
        $utilisateur->setPrenom('User');
        $utilisateur->setPassword('hashed_password'); 
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setCleUtilisateur(uniqid('user_key_'));
        $utilisateur->setEmailVerificationCode('code_test');
        $utilisateur->setEmailVerificationRequestedAt(new \DateTimeImmutable());
        $utilisateur->setIsVerified(true);
        return $utilisateur;
    }

    private function createDetailsBillet(string $ref = 'REF123'): array
    {
        return [
            'nom_evenement' => 'Concert JO',
            'reference_billet' => $ref,
            'date_evenement' => '2024-08-01',
            'heure_evenement' => '20:00',
            'lieu_evenement' => 'Stade de France',
            'categorie_billet' => 'Catégorie A',
            'prix_billet' => 150.00,
            'quantite' => 1,
        ];
    }

    public function testEnvoyerBilletParEmailSuccessWithoutQrCode(): void
    {
        $utilisateur = $this->createMockUtilisateur();
        $detailsBillet = $this->createDetailsBillet();

       
        $this->twigMock->expects($this->once())
            ->method('render')
            ->with(
                'emails/billet_achat.html.twig',
                $this->callback(function($context) use ($detailsBillet, $utilisateur) {
                    $this->assertEquals($detailsBillet, $context['details_billet']);
                    $this->assertEquals($utilisateur, $context['utilisateur']);
                    return true;
                })
            )
            ->willReturn('<html>Billet Email Content Without QR</html>');

       
        $this->mailerMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($utilisateur, $detailsBillet) {
                $this->assertEquals('no-reply@appjo2024.com', $email->getFrom()[0]->getAddress());
                $this->assertEquals($utilisateur->getEmail(), $email->getTo()[0]->getAddress());
                $this->assertEquals('Votre billet pour ' . $detailsBillet['nom_evenement'], $email->getSubject());
                $this->assertEquals('<html>Billet Email Content Without QR</html>', $email->getHtmlBody());
                $this->assertCount(0, $email->getAttachments(), 'L\'email ne doit pas avoir de pièce jointe.');
                return true;
            }));

        
        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with($this->stringContains(sprintf('Billet envoyé par e-mail à %s pour l\'événement %s (référence: %s)', $utilisateur->getEmail(), $detailsBillet['nom_evenement'], $detailsBillet['reference_billet'])));
        
        $this->loggerMock->expects($this->never())->method('error');


        $this->billetSenderService->envoyerBilletParEmail($utilisateur, $detailsBillet);
    }

    public function testEnvoyerBilletParEmailSuccessWithQrCode(): void
    {
        $utilisateur = $this->createMockUtilisateur();
        $detailsBillet = $this->createDetailsBillet('REF456');
        $cheminQRCode = '/tmp/fake_qrcode.png'; 
        
        file_put_contents($cheminQRCode, 'fake_qr_code_content');

        $this->twigMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Billet Email Content With QR</html>');

        $this->mailerMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($utilisateur, $detailsBillet) {
                $this->assertEquals($utilisateur->getEmail(), $email->getTo()[0]->getAddress());
                $this->assertEquals('Votre billet pour ' . $detailsBillet['nom_evenement'], $email->getSubject());
                $this->assertCount(1, $email->getAttachments(), 'L\'email doit avoir une pièce jointe.');
                
               
                $attachment = $email->getAttachments()[0];
                $this->assertEquals('qrcode_REF456.png', $attachment->getFilename());
                $this->assertEquals('image/png', $attachment->getContentType()); 
                $this->assertEquals('inline', $attachment->getDisposition()); 
                return true;
            }));
        
        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with($this->stringContains(sprintf('Billet envoyé par e-mail à %s pour l\'événement %s (référence: %s)', $utilisateur->getEmail(), $detailsBillet['nom_evenement'], $detailsBillet['reference_billet'])));
        $this->loggerMock->expects($this->never())->method('error');

        $this->billetSenderService->envoyerBilletParEmail($utilisateur, $detailsBillet, $cheminQRCode);

        
        unlink($cheminQRCode);
    }

    public function testEnvoyerBilletParEmailFailure(): void
    {
        $utilisateur = $this->createMockUtilisateur();
        $detailsBillet = $this->createDetailsBillet('REF789');

        $this->twigMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Billet Email Content Failure</html>');

        
        $this->mailerMock->expects($this->once())
            ->method('send')
            ->willThrowException(new \RuntimeException('Mail server connection failed'));

        
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->stringContains(sprintf('Erreur lors de l\'envoi de l\'e-mail du billet à %s pour l\'événement %s (référence: %s): Mail server connection failed', $utilisateur->getEmail(), $detailsBillet['nom_evenement'], $detailsBillet['reference_billet'])));
        $this->loggerMock->expects($this->never())->method('info');

        $this->billetSenderService->envoyerBilletParEmail($utilisateur, $detailsBillet);
    }
}