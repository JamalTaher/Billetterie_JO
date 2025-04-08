<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Service\EmailVerificationService; // Importez le service EmailVerificationService
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid; 

class RegistrationController extends AbstractController
{
    private $emailVerificationService; 

    
    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
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

            // Générer et envoyer l'e-mail de vérification
            $verificationCode = $this->emailVerificationService->generateVerificationCode($utilisateur);
            $this->emailVerificationService->sendVerificationEmail($utilisateur, $verificationCode);

            
            $this->addFlash('success', 'Votre compte a été créé. Veuillez vérifier votre boîte de réception pour confirmer votre adresse e-mail.');
            return $this->redirectToRoute('app_verify_email_page');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}