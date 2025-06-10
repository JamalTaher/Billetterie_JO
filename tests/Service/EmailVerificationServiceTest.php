<?php

namespace App\Tests\Service;

use App\Service\EmailVerificationService;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository; 
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface; 
use Symfony\Component\Mime\Email; 
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface; 
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface; 
use Twig\Environment as TwigEnvironment; 
use Psr\Log\LoggerInterface; 

class EmailVerificationServiceTest extends KernelTestCase
{
    private EmailVerificationService $emailVerificationService;
    private $entityManagerMock;
    private $mailerMock;
    private $urlGeneratorMock;
    private $tokenStorageMock; 
    private $authorizationCheckerMock; 
    private $twigMock; 
    private $loggerMock; 

    protected function setUp(): void
    {
        
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->mailerMock = $this->createMock(MailerInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);
        $this->tokenStorageMock = $this->createMock(TokenStorageInterface::class);
        $this->authorizationCheckerMock = $this->createMock(AuthorizationCheckerInterface::class);
        $this->twigMock = $this->createMock(TwigEnvironment::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        
        $this->emailVerificationService = new EmailVerificationService(
            $this->entityManagerMock,
            $this->mailerMock,
            $this->urlGeneratorMock,
            $this->tokenStorageMock,
            $this->authorizationCheckerMock,
            $this->twigMock,
            $this->loggerMock
        );
    }

    public function testGenerateVerificationCode(): void
    {
        $user = new Utilisateur(); 
        $code = $this->emailVerificationService->generateVerificationCode($user);
        $this->assertIsString($code);
        $this->assertNotEmpty($code);
        
    }

    public function testSendVerificationEmailSuccess(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('test_success@example.com');
        $utilisateur->setNom('John');
        $utilisateur->setPrenom('Doe');
        $utilisateur->setPassword('hashed');
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setCleUtilisateur(uniqid());

        $verificationCode = 'mock_code_123'; 
        $verificationUrl = 'http://localhost/verify/email/mock_code_123'; 

        
        $this->urlGeneratorMock->expects($this->once())
            ->method('generate')
            ->with(
                'app_verify_email',
                ['code' => $verificationCode, 'email' => $utilisateur->getEmail()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            ->willReturn($verificationUrl);

        
        $this->twigMock->expects($this->once())
            ->method('render')
            ->with(
                'security/verify_email_content.html.twig',
                $this->callback(function($context) use ($verificationUrl) {
                    return $context['verificationUrl'] === $verificationUrl &&
                           isset($context['tokenLifetime']);
                })
            )
            ->willReturn('<html>email content</html>'); 

       
        $this->mailerMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($utilisateur, $verificationUrl) {
                $this->assertEquals('no-reply@appjo2024.com', $email->getFrom()[0]->getAddress());
                $this->assertEquals($utilisateur->getEmail(), $email->getTo()[0]->getAddress());
                $this->assertEquals('Confirmez votre adresse email', $email->getSubject());
                $this->assertEquals('<html>email content</html>', $email->getHtmlBody());
                return true;
            }));

        
        $this->entityManagerMock->expects($this->once())->method('persist')->with($utilisateur);
        $this->entityManagerMock->expects($this->once())->method('flush');

       
        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(sprintf('E-mail de vérification renvoyé à %s', $utilisateur->getEmail()));

        $this->emailVerificationService->sendVerificationEmail($utilisateur, $verificationCode); 

       
        $this->assertEquals($verificationCode, $utilisateur->getEmailVerificationCode());
        $this->assertNotNull($utilisateur->getEmailVerificationRequestedAt());
    }

    public function testSendVerificationEmailFailure(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('test_failure@example.com');
        $utilisateur->setNom('Jane');
        $utilisateur->setPrenom('Doe');
        $utilisateur->setPassword('hashed');
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setCleUtilisateur(uniqid());

        $verificationCode = 'mock_code_failure';
        $verificationUrl = 'http://localhost/verify/email/mock_code_failure';

        $this->urlGeneratorMock->expects($this->once())
            ->method('generate')
            ->willReturn($verificationUrl);

        $this->twigMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>email content</html>');

        
        $this->mailerMock->expects($this->once())
            ->method('send')
            ->willThrowException(new \RuntimeException('Mail server error'));

        $this->entityManagerMock->expects($this->once())->method('persist')->with($utilisateur);
        $this->entityManagerMock->expects($this->once())->method('flush');

        
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Erreur lors de l\'envoi de l\'e-mail de vérification'));

        $this->emailVerificationService->sendVerificationEmail($utilisateur, $verificationCode);

        $this->assertEquals($verificationCode, $utilisateur->getEmailVerificationCode());
        $this->assertNotNull($utilisateur->getEmailVerificationRequestedAt());
    }

    public function testIsVerificationCodeValid(): void
    {
        $user = new Utilisateur();
        $user->setEmailVerificationCode('valid_code');
        $user->setEmailVerificationRequestedAt(new \DateTimeImmutable()); 

        
        $this->assertTrue($this->emailVerificationService->isVerificationCodeValid($user, 'valid_code'), 'Le code valide devrait être reconnu.');

       
        $this->assertFalse($this->emailVerificationService->isVerificationCodeValid($user, 'incorrect_code'), 'Le code incorrect ne devrait pas être reconnu.');

       
        $user->setEmailVerificationCode(null);
        $this->assertFalse($this->emailVerificationService->isVerificationCodeValid($user, 'valid_code'), 'Le code manquant ne devrait pas être reconnu.');

       
        $user->setEmailVerificationCode('valid_code');
        $user->setEmailVerificationRequestedAt(null);
        $this->assertFalse($this->emailVerificationService->isVerificationCodeValid($user, 'valid_code'), 'La date de demande manquante ne devrait pas être reconnue.');

        
        $user->setEmailVerificationCode('valid_code');
        $user->setEmailVerificationRequestedAt(new \DateTimeImmutable('-20 minutes')); 
        $this->assertFalse($this->emailVerificationService->isVerificationCodeValid($user, 'valid_code'), 'Le code expiré ne devrait pas être reconnu.');
    }

    public function testMarkEmailAsVerified(): void
    {
        $user = new Utilisateur();
        $user->setEmailVerificationCode('some_code');
        $user->setEmailVerificationRequestedAt(new \DateTimeImmutable());
        $user->setIsVerified(false);

        $this->entityManagerMock->expects($this->once())->method('persist')->with($user);
        $this->entityManagerMock->expects($this->once())->method('flush');

        $this->emailVerificationService->markEmailAsVerified($user);

        $this->assertTrue($user->isVerified(), 'L\'email devrait être marqué comme vérifié.');
        $this->assertNull($user->getEmailVerificationCode(), 'Le code de vérification devrait être nullifié.');
        $this->assertNull($user->getEmailVerificationRequestedAt(), 'La date de demande devrait être nullifiée.');
    }

    public function testResendVerificationEmail(): void
    {
        $user = new Utilisateur();
        $user->setEmail('resend@example.com');
        $user->setNom('Resend');
        $user->setPrenom('User');
        $user->setPassword('hashed');
        $user->setRoles(['ROLE_USER']);
        $user->setCleUtilisateur(uniqid());

        
        $verificationUrl = 'http://localhost/verify/email/new_mock_code';
        $this->urlGeneratorMock->expects($this->once())
            ->method('generate')
            ->willReturn($verificationUrl);

        $this->twigMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>resend email content</html>');

        $this->mailerMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($user) {
                $this->assertEquals($user->getEmail(), $email->getTo()[0]->getAddress());
                return true;
            }));

        $this->entityManagerMock->expects($this->exactly(2))->method('persist')->with($user); 
        $this->entityManagerMock->expects($this->exactly(2))->method('flush'); 

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with($this->stringContains('E-mail de vérification renvoyé à resend@example.com'));

        $this->emailVerificationService->resendVerificationEmail($user);

        $this->assertNotNull($user->getEmailVerificationCode());
        $this->assertNotNull($user->getEmailVerificationRequestedAt());
    }
}