<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\CategoryFilterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $offres = $entityManager->getRepository(Offre::class)
            ->createQueryBuilder('o')
            ->leftJoin('o.evenement', 'e')
            ->addSelect('e')
            ->setMaxResults(3) // Afficher seulement 3 offres sur la page d'accueil (exemple)
            ->getQuery()
            ->getResult();

        return $this->render('main/home.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/offres', name: 'app_offres', methods: ['GET', 'POST'])]
    public function offres(EntityManagerInterface $entityManager, Request $request): Response
    {
        $categoryForm = $this->createForm(CategoryFilterType::class);
        $categoryForm->handleRequest($request);
        $categorie = $categoryForm->get('categorie')->getData();

        $queryBuilder = $entityManager->getRepository(Offre::class)
            ->createQueryBuilder('o')
            ->leftJoin('o.evenement', 'e')
            ->addSelect('e');

        if ($categorie) {
            $queryBuilder->andWhere('e.categorie = :categorie')
                ->setParameter('categorie', $categorie);
        }

        $offres = $queryBuilder->getQuery()->getResult();

        return $this->render('main/offres.html.twig', [
            'offres' => $offres,
            'categoryForm' => $categoryForm->createView(),
        ]);
    }

    #[Route('/offres/filter', name: 'app_offres_filter', methods: ['POST'], condition: 'request.isXmlHttpRequest()')]
    public function filterOffres(EntityManagerInterface $entityManager, Request $request): Response
    {
        $categoryForm = $this->createForm(CategoryFilterType::class);
        $categoryForm->handleRequest($request);

        $queryBuilder = $entityManager->getRepository(Offre::class)
            ->createQueryBuilder('o')
            ->leftJoin('o.evenement', 'e')
            ->addSelect('e');

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $categorie = $categoryForm->get('categorie')->getData();
            if ($categorie) {
                $queryBuilder->andWhere('e.categorie = :categorie')
                    ->setParameter('categorie', $categorie);
            }
        }

        $offres = $queryBuilder->getQuery()->getResult();

        return $this->render('main/_offres_list.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'app_panier_ajouter', methods: ['POST'], condition: 'request.isXmlHttpRequest()')]
    public function ajouterAuPanier(int $id, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $offre = $entityManager->getRepository(Offre::class)->find($id);

        if (!$offre) {
            return new JsonResponse(['success' => false, 'message' => 'Offre non trouvée.']);
        }

        $panier = $session->get('panier', []);

        if (isset($panier[$offre->getId()])) {
            $panier[$offre->getId()]++; // Si l'offre est déjà dans le panier, on augmente la quantité
        } else {
            $panier[$offre->getId()] = 1; // Sinon, on l'ajoute avec une quantité de 1
        }

        $session->set('panier', $panier);

        return new JsonResponse(['success' => true, 'message' => 'Offre ajoutée au panier.']);
    }

    #[Route('/panier', name: 'app_panier_voir')]
    public function voirPanier(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $panierAvecDetails = [];
        $total = 0;

        foreach ($panier as $offreId => $quantite) {
            $offre = $entityManager->getRepository(Offre::class)->find($offreId);
            if ($offre) {
                $panierAvecDetails[] = [
                    'offre' => $offre,
                    'quantite' => $quantite,
                ];
                $total += $offre->getPrix() * $quantite;
            }
        }

        return $this->render('main/panier.html.twig', [
            'panierAvecDetails' => $panierAvecDetails,
            'total' => $total,
        ]);
    }
}