<?php

namespace App\Controller\Admin;

use App\Entity\Evenement;
use App\Form\EvenementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/evenements', name: 'evenements_index')]
    public function evenementsIndex(EntityManagerInterface $entityManager): Response
    {
        $evenements = $entityManager->getRepository(Evenement::class)->findAll();

        return $this->render('admin/evenements/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/evenements/creer', name: 'evenements_creer')]
    public function evenementsCreer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès.');

            return $this->redirectToRoute('admin_evenements_index');
        }

        return $this->render('admin/evenements/creer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evenements/modifier/{id}', name: 'evenements_modifier', requirements: ['id' => '\d+'])]
    public function evenementsModifier(Request $request, EntityManagerInterface $entityManager, Evenement $evenement): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été modifié avec succès.');

            return $this->redirectToRoute('admin_evenements_index');
        }

        return $this->render('admin/evenements/modifier.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }

    #[Route('/evenements/supprimer/{id}', name: 'evenements_supprimer', requirements: ['id' => '\d+'])]
    public function evenementsSupprimer(EntityManagerInterface $entityManager, Evenement $evenement): Response
    {
        $entityManager->remove($evenement);
        $entityManager->flush();

        $this->addFlash('success', 'L\'événement a été supprimé avec succès.');

        return $this->redirectToRoute('admin_evenements_index');
    }
}