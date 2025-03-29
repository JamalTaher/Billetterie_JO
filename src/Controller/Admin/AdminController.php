<?php

namespace App\Controller\Admin;


use App\Entity\Evenement;
use App\Entity\Offre;
use App\Entity\TypeOffre;
use App\Form\EvenementType;
use App\Form\OffreType;
use App\Form\TypeOffreType;
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

    #[Route('/offres', name: 'offres_index')]
    public function offresIndex(EntityManagerInterface $entityManager): Response
    {
        $offres = $entityManager->getRepository(Offre::class)->findAll();

        return $this->render('admin/offres/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/offres/creer', name: 'offres_creer')]
    public function offresCreer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();

            $this->addFlash('success', 'L\'offre a été créée avec succès.');

            return $this->redirectToRoute('admin_offres_index');
        }

        return $this->render('admin/offres/creer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/offres/modifier/{id}', name: 'offres_modifier', requirements: ['id' => '\d+'])]
    public function offresModifier(Request $request, EntityManagerInterface $entityManager, Offre $offre): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'offre a été modifiée avec succès.');

            return $this->redirectToRoute('admin_offres_index');
        }

        return $this->render('admin/offres/modifier.html.twig', [
            'form' => $form->createView(),
            'offre' => $offre,
        ]);
    }

    #[Route('/offres/supprimer/{id}', name: 'offres_supprimer', requirements: ['id' => '\d+'])]
    public function offresSupprimer(EntityManagerInterface $entityManager, Offre $offre): Response
    {
        $entityManager->remove($offre);
        $entityManager->flush();

        $this->addFlash('success', 'L\'offre a été supprimée avec succès.');

        return $this->redirectToRoute('admin_offres_index');
    }

    #[Route('/types-offres', name: 'types_offres_index')]
    public function typesOffresIndex(EntityManagerInterface $entityManager): Response
    {
        $typesOffres = $entityManager->getRepository(TypeOffre::class)->findAll();

        return $this->render('admin/types_offres/index.html.twig', [
            'typesOffres' => $typesOffres,
        ]);
    }

    #[Route('/types-offres/creer', name: 'types_offres_creer')]
    public function typesOffresCreer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeOffre = new TypeOffre();
        $form = $this->createForm(TypeOffreType::class, $typeOffre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeOffre);
            $entityManager->flush();

            $this->addFlash('success', 'Le type d\'offre a été créé avec succès.');

            return $this->redirectToRoute('admin_types_offres_index');
        }

        return $this->render('admin/types_offres/creer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/types-offres/modifier/{id}', name: 'types_offres_modifier', requirements: ['id' => '\d+'])]
    public function typesOffresModifier(Request $request, EntityManagerInterface $entityManager, TypeOffre $typeOffre): Response
    {
        $form = $this->createForm(TypeOffreType::class, $typeOffre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le type d\'offre a été modifié avec succès.');

            return $this->redirectToRoute('admin_types_offres_index');
        }

        return $this->render('admin/types_offres/modifier.html.twig', [
            'form' => $form->createView(),
            'typeOffre' => $typeOffre,
        ]);
    }

    #[Route('/types-offres/supprimer/{id}', name: 'types_offres_supprimer', requirements: ['id' => '\d+'])]
    public function typesOffresSupprimer(EntityManagerInterface $entityManager, TypeOffre $typeOffre): Response
    {
        $entityManager->remove($typeOffre);
        $entityManager->flush();

        $this->addFlash('success', 'Le type d\'offre a été supprimé avec succès.');

        return $this->redirectToRoute('admin_types_offres_index');
    }
}