<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Service\EmailVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Psr\Log\LoggerInterface; 

class RegistrationController extends AbstractController
{
    private $emailVerificationService;
    private LoggerInterface $logger; 

    public function __construct(EmailVerificationService $emailVerificationService, LoggerInterface $logger) 
    {
        $this->emailVerificationService = $emailVerificationService;
        $this->logger = $logger; 
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $form->get('plainPassword')->getData()
                )
            );

            $utilisateur->setCleUtilisateur(Uuid::v4());

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $verificationCode = $this->emailVerificationService->generateVerificationCode($utilisateur);

            try {
                $this->emailVerificationService->sendVerificationEmail($utilisateur, $verificationCode);
                $this->logger->info(sprintf('E-mail de vérification envoyé à %s', $utilisateur->getEmail()));
                $this->addFlash('success', 'Votre compte a été créé. Veuillez vérifier votre boîte de réception pour confirmer votre adresse e-mail.');
                return $this->redirectToRoute('app_verify_email_page');
            } catch (\Throwable $e) {
                $this->logger->error(sprintf('Erreur lors de l\'envoi de l\'e-mail de vérification à %s: %s', $utilisateur->getEmail(), $e->getMessage()));
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de l\'e-mail de vérification. Veuillez réessayer plus tard.');
                return $this->redirectToRoute('app_register'); 
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}