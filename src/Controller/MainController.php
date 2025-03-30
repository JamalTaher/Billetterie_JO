<?php

namespace App\Controller;

use App\Entity\Evenement; // Ajout de cette ligne
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
            ->leftJoin('o.prixOffreEvenements', 'poe')
            ->addSelect('poe')
            ->leftJoin('poe.evenement', 'e')
            ->addSelect('e')
            ->setMaxResults(3)
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
            ->leftJoin('o.prixOffreEvenements', 'poe')
            ->addSelect('poe')
            ->leftJoin('poe.evenement', 'e')
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
            ->leftJoin('o.prixOffreEvenements', 'poe')
            ->addSelect('poe')
            ->leftJoin('poe.evenement', 'e')
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

    #[Route('/panier/ajouter/{offreId}/{evenementId}', name: 'app_panier_ajouter', methods: ['POST'], condition: 'request.isXmlHttpRequest()')]
    public function ajouterAuPanier(int $offreId, int $evenementId, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $offre = $entityManager->getRepository(Offre::class)->find($offreId);
        $evenement = $entityManager->getRepository(Evenement::class)->find($evenementId); // Modifié ici

        if (!$offre || !$evenement) {
            return new JsonResponse(['success' => false, 'message' => 'Offre ou événement non trouvé.']);
        }

        $panierKey = $offreId . '_' . $evenementId;
        $panier = $session->get('panier', []);

        if (isset($panier[$panierKey])) {
            $panier[$panierKey]++;
        } else {
            $panier[$panierKey] = 1;
        }

        $session->set('panier', $panier);

      
        $panierCount = array_sum($panier);

        return new JsonResponse(['success' => true, 'message' => 'Offre ajoutée au panier.', 'panierCount' => $panierCount]); // Modifié ici
    }

    #[Route('/panier', name: 'app_panier_voir')]
    public function voirPanier(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $panierAvecDetails = [];
        $total = 0;

        foreach ($panier as $panierKey => $quantite) {
            [$offreId, $evenementId] = explode('_', $panierKey);
            $offre = $entityManager->getRepository(Offre::class)->find($offreId);
            $evenement = $entityManager->getRepository(Evenement::class)->find($evenementId); // Modifié ici

            if ($offre && $evenement) {
                $prixOffreEvenement = $entityManager->getRepository(\App\Entity\PrixOffreEvenement::class)
                    ->findOneBy(['offre' => $offre, 'evenement' => $evenement]);

                if ($prixOffreEvenement) {
                    $panierAvecDetails[] = [
                        'offre' => $offre,
                        'evenement' => $evenement,
                        'quantite' => $quantite,
                        'prix' => $prixOffreEvenement->getPrix(),
                    ];
                    $total += $prixOffreEvenement->getPrix() * $quantite;
                }
            }
        }

        return $this->render('main/panier.html.twig', [
            'panierAvecDetails' => $panierAvecDetails,
            'total' => $total,
        ]);
    }
}