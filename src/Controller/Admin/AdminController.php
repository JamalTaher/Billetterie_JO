<?php

namespace App\Controller\Admin;

use App\Entity\Evenement;
use App\Entity\Commande;
use App\Form\Admin\EvenementType;
use App\Repository\CommandeRepository;
use App\Repository\EvenementRepository; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted; 


#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(private CommandeRepository $commandeRepository)
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/evenements', name: 'evenements_list')]
    #[Route('/evenements/category/{category}', name: 'evenement_by_category')]
    public function listEvenements(?string $category = null, EvenementRepository $evenementRepository): Response
    {
        if ($category) {
            $evenements = $evenementRepository->findBy(['categorie' => $category]);
        } else {
            $evenements = $evenementRepository->findAll();
        }

        return $this->render('admin/evenements/index.html.twig', [
            'evenements' => $evenements,
            'currentCategory' => $category,
        ]);
    }

    #[Route('/categories', name: 'select_category')]
    public function selectCategory(EvenementRepository $evenementRepository): Response
    {
        $categoriesAssoc = $evenementRepository->getAllCategories();
        $categories = array_map(function ($item) {
            return $item['categorie'];
        }, $categoriesAssoc);

        return $this->render('admin/evenements/select_category.html.twig', [
            'categories' => $categories,
        ]);
    }


    #[Route('/evenement/new', name: 'evenement_new')]
    public function newEvenement(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        
        $form = $this->createForm(EvenementType::class, $evenement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès.');

            return $this->redirectToRoute('admin_evenements_list');
        }

        return $this->render('admin/evenements/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evenement/edit/{id}', name: 'evenement_edit', requirements: ['id' => '\d+'])]
    public function editEvenement(Request $request, EntityManagerInterface $entityManager, Evenement $evenement): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été modifié avec succès.');

            return $this->redirectToRoute('admin_evenements_list');
        }

        return $this->render('admin/evenements/edit.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }

    #[Route('/evenement/delete/{id}', name: 'evenement_delete', requirements: ['id' => '\d+'])]
    public function deleteEvenement(EntityManagerInterface $entityManager, Evenement $evenement): Response
    {
        $prixOffreEvenements = $evenement->getPrixOffreEvenements();

        foreach ($prixOffreEvenements as $prixOffreEvenement) {
            $commandes = $entityManager->getRepository(Commande::class)->findBy(['prixOffreEvenement' => $prixOffreEvenement]);
            foreach ($commandes as $commande) {
                $commande->setPrixOffreEvenement(null);
                $entityManager->persist($commande);
            }
        }
        $entityManager->flush(); 

        $entityManager->remove($evenement);
        $entityManager->flush();

        $this->addFlash('success', 'L\'événement a été supprimé avec succès.');

        return $this->redirectToRoute('admin_evenements_list');
    }

    #[Route('/stats', name: 'stats_index')]
    public function statsIndex(): Response
    {
        $ventesParEvenement = $this->commandeRepository->getVentesParEvenementAvecTotal();

        return $this->render('admin/stats/index.html.twig', [
            'ventesParEvenement' => $ventesParEvenement,
        ]);
    }
}