<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Attribute\IsGranted; // Import de IsGranted
use App\Entity\Offre;
use App\Form\OffreType; // CHANGÉ : Utilise App\Form\OffreType (le bon)
// use App\Form\Offre1Type; // COMMENTÉ/SUPPRIMÉ : N'est plus utilisé
use App\Repository\OffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/offre')] // MODIFIÉ : Ajout du préfixe '/admin'
#[IsGranted('ROLE_ADMIN')] // AJOUTÉ : Protection de l'accès au contrôleur
final class OffreController extends AbstractController
{
    #[Route('/', name: 'app_offre_index', methods: ['GET'])] // MODIFIÉ : '/offre' devient '/'
    public function index(OffreRepository $offreRepository): Response
    {
        return $this->render('offre/index.html.twig', [
            'offres' => $offreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre); // CHANGÉ : Utilise OffreType::class
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();

            // Ajout d'un flash message pour confirmer l'ajout
            $this->addFlash('success', 'L\'offre a été créée avec succès.');

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_show', methods: ['GET'])]
    public function show(Offre $offre): Response
    {
        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreType::class, $offre); // CHANGÉ : Utilise OffreType::class
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Ajout d'un flash message pour confirmer la modification
            $this->addFlash('success', 'L\'offre a été modifiée avec succès.');

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
            // Ajout d'un flash message pour confirmer la suppression
            $this->addFlash('success', 'L\'offre a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }
}