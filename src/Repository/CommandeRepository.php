<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function getVentesParEvenementAvecTotal(): array
    {
        return $this->createQueryBuilder('c')
            ->select('e.nom AS evenement_nom, COUNT(c.id) AS nombre_ventes, SUM(poe.prix) AS montant_total')
            ->join('c.prixOffreEvenement', 'poe')
            ->join('poe.evenement', 'e')
            ->groupBy('e.id')
            ->orderBy('nombre_ventes', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

   
}