<?php

namespace App\Repository;

use App\Entity\PrixOffreEvenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrixOffreEvenement>
 */
class PrixOffreEvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrixOffreEvenement::class);
    }

    
}
