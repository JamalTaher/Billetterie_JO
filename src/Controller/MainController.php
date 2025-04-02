<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Offre;
use App\Entity\Commande;
use App\Entity\PrixOffreEvenement;
use App\Entity\Utilisateur;
use App\Form\CategoryFilterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
        $evenement = $entityManager->getRepository(Evenement::class)->find($evenementId);

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

        return new JsonResponse(['success' => true, 'message' => 'Offre ajoutée au panier.', 'panierCount' => $panierCount]);
    }

    #[Route('/panier/supprimer/{offreId}/{evenementId}', name: 'app_panier_supprimer', methods: ['POST'], condition: 'request.isXmlHttpRequest()')]
    public function supprimerDuPanier(int $offreId, int $evenementId, SessionInterface $session): Response
    {
        $panierKey = $offreId . '_' . $evenementId;
        $panier = $session->get('panier', []);

        if (isset($panier[$panierKey])) {
            unset($panier[$panierKey]);
            $session->set('panier', $panier);


            $panierCount = array_sum($panier);

            return new JsonResponse(['success' => true, 'message' => 'Élément supprimé du panier.', 'panierCount' => $panierCount]);
        } else {
            return new JsonResponse(['success' => false, 'message' => 'L\'élément n\'a pas été trouvé dans le panier.']);
        }
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
            $evenement = $entityManager->getRepository(Evenement::class)->find($evenementId);

            if ($offre && $evenement) {
                $prixOffreEvenement = $entityManager->getRepository(PrixOffreEvenement::class)
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

    #[Route('/panier/checkout', name: 'app_panier_checkout')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function checkout(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $panierAvecInfos = [];
        $total = 0;

        foreach ($panier as $panierKey => $quantite) {
            [$offreId, $evenementId] = explode('_', $panierKey);
            $offre = $entityManager->getRepository(Offre::class)->find($offreId);
            $evenement = $entityManager->getRepository(Evenement::class)->find($evenementId);

            if ($offre && $evenement) {
                $prixOffreEvenement = $entityManager->getRepository(PrixOffreEvenement::class)
                    ->findOneBy(['offre' => $offre, 'evenement' => $evenement]);

                if ($prixOffreEvenement) {
                    $panierAvecInfos[] = [
                        'offre' => $offre,
                        'evenement' => $evenement,
                        'quantite' => $quantite,
                        'prix' => $prixOffreEvenement->getPrix(),
                        'total' => $prixOffreEvenement->getPrix() * $quantite,
                    ];
                    $total += $prixOffreEvenement->getPrix() * $quantite;
                }
            }
        }

        return $this->render('main/checkout.html.twig', [
            'panierAvecInfos' => $panierAvecInfos,
            'total' => $total,
        ]);
    }

    #[Route('/panier/process_payment', name: 'app_panier_process_payment', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function processPayment(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $utilisateur = $this->getUser();

        if (!$utilisateur instanceof Utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour effectuer le paiement.');
            return $this->redirectToRoute('app_login');
        }

        $achats = [];

        if (!empty($panier)) {
            $userKey = $utilisateur->getCleUtilisateur();

            foreach ($panier as $panierKey => $quantite) {
                [$offreId, $evenementId] = explode('_', $panierKey);
                $offre = $entityManager->getRepository(Offre::class)->find($offreId);
                $evenement = $entityManager->getRepository(Evenement::class)->find($evenementId);
                $prixOffreEvenement = $entityManager->getRepository(PrixOffreEvenement::class)
                    ->findOneBy(['offre' => $offre, 'evenement' => $evenement]);

                if ($offre && $evenement && $prixOffreEvenement) {
                    for ($i = 0; $i < $quantite; $i++) {
                        $purchaseKey = bin2hex(random_bytes(16));
                        $finalTicketKey = $userKey . '-' . $purchaseKey;

                        $commande = new Commande();
                        $commande->setPrixOffreEvenement($prixOffreEvenement);
                        $commande->setUtilisateur($utilisateur);
                        $commande->setDateAchat(new \DateTimeImmutable());
                        $commande->setCleAchat($purchaseKey);
                        $commande->setCleBillet($finalTicketKey);

                        $entityManager->persist($commande);
                        $achats[] = $commande;
                    }
                }
            }

            $entityManager->flush();
            $session->remove('panier');

            if (!empty($achats)) {
                return $this->render('main/payment_success.html.twig', [
                    'achats' => $achats,
                ]);
            } else {
                $this->addFlash('warning', 'Votre panier était vide lors de la tentative de paiement.');
                return $this->redirectToRoute('app_panier_voir');
            }
        } else {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_voir');
        }
    }
}