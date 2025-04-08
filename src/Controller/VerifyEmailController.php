<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\EmailVerificationService; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VerifyEmailController extends AbstractController
{
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UtilisateurRepository $utilisateurRepository, EmailVerificationService $emailVerificationService): Response
    {
        $code = $request->query->get('code');
        $email = $request->query->get('email');

        if (null === $code || null === $email) {
            $this->addFlash('error', 'Code de vérification ou email invalide.');
            return $this->redirectToRoute('app_login');
        }

        $user = $utilisateurRepository->findOneBy(['email' => $email]);

        if (null === $user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_login');
        }

        if ($emailVerificationService->isVerificationCodeValid($user, $code)) {
          
            $emailVerificationService->markEmailAsVerified($user);
           
            $this->addFlash('success', 'Votre adresse email a été vérifiée avec succès ! Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_home'); 
        }

        $this->addFlash('error', 'Votre lien a expiré. Veuillez réessayer.');
        return $this->redirectToRoute('app_verify_email_page');
    }

    #[Route('/verify/email/page', name: 'app_verify_email_page')]
    public function showVerificationPage(): Response
    {
        return $this->render('security/verify_email.html.twig'); 
    }

    #[Route('/verify/email/resend', name: 'app_resend_verification_email')]
    public function resendVerificationEmail(EmailVerificationService $emailVerificationService): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        if ($user) {
            $emailVerificationService->resendVerificationEmail($user);
            $this->addFlash('info', 'Un nouveau code de vérification a été envoyé à votre adresse email.');
        } else {
            $this->addFlash('error', 'Utilisateur non connecté.');
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('app_verify_email_page');
    }
}