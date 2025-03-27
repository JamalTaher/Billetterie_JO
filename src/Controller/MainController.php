<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\CategoryFilterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route('/offres', name: 'app_offres')]
    public function offres(EntityManagerInterface $entityManager, Request $request): Response
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

        return $this->render('main/offres.html.twig', [
            'offres' => $offres,
            'categoryForm' => $categoryForm->createView(),
        ]);
    }
}