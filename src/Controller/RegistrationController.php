<?php

namespace App\Controller;

use App\Entity\Utilisateur; // Utilisez le nom correct de votre entité
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid; // Importez la classe Uuid

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur(); // Utilisez le nom correct de votre entité
        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $form->get('plainPassword')->getData()
                )
            );

            // Générer une clé utilisateur unique (UUID)
            $utilisateur->setCleUtilisateur(Uuid::v4());

            $entityManager->persist($utilisateur);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}