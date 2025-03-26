<?php

namespace App\Controller;

use App\Entity\Offre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $offres = $entityManager->getRepository(Offre::class)->findAll();

        return $this->render('main/home.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/offres', name: 'app_offres')]
    public function offres(EntityManagerInterface $entityManager): Response
    {
        $offres = $entityManager->getRepository(Offre::class)->findAll();

        return $this->render('main/offres.html.twig', [
            'offres' => $offres,
        ]);
    }
}