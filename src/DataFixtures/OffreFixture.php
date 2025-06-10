<?php

namespace App\DataFixtures;

use App\Entity\Evenement;
use App\Entity\Offre; 
use App\Entity\PrixOffreEvenement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OffreFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // --- Creation des evenements  ---
        $evenement1 = new Evenement();
        $evenement1->setNom('Concert Rock Stade de France');
        $evenement1->setDate(new \DateTimeImmutable('+1 month'));
        $evenement1->setLieu('Stade de France');
        $evenement1->setCategorie('Musique');
        $manager->persist($evenement1);

        $evenement2 = new Evenement();
        $evenement2->setNom('Compétition Athlétisme JO');
        $evenement2->setDate(new \DateTimeImmutable('+2 months'));
        $evenement2->setLieu('Stade Olympique');
        $evenement2->setCategorie('Sport');
        $manager->persist($evenement2);

        $evenement3 = new Evenement();
        $evenement3->setNom('Cérémonie de Clôture JO');
        $evenement3->setDate(new \DateTimeImmutable('+3 months'));
        $evenement3->setLieu('Champ de Mars');
        $evenement3->setCategorie('Cérémonie');
        $manager->persist($evenement3);

        // --- Créer quelques Offres ---
        // Offre 1 : Billet VIP pour le concert
        $offre1 = new Offre();
        $offre1->setNom('Billet Catégorie A');
        $offre1->setDescription('Accès VIP avec restauration');
        $offre1->setCapacite(100); 
        $manager->persist($offre1);

        $prixOffreEvenement1 = new PrixOffreEvenement();
        $prixOffreEvenement1->setOffre($offre1);
        $prixOffreEvenement1->setEvenement($evenement1);
        $prixOffreEvenement1->setPrix(150.00);
        $manager->persist($prixOffreEvenement1);

        // Offre 2 : Billet  pour l'athlétisme
        $offre2 = new Offre();
        $offre2->setNom('Billet Standard');
        $offre2->setDescription('Accès général aux tribunes');
        $offre2->setCapacite(500); 
        $manager->persist($offre2);

        $prixOffreEvenement2 = new PrixOffreEvenement();
        $prixOffreEvenement2->setOffre($offre2);
        $prixOffreEvenement2->setEvenement($evenement2);
        $prixOffreEvenement2->setPrix(50.00);
        $manager->persist($prixOffreEvenement2);

        // Offre 3 : Pack Famille pour la cérémonie
        $offre3 = new Offre();
        $offre3->setNom('Pack Famille');
        $offre3->setDescription('2 adultes + 2 enfants pour la cérémonie');
        $offre3->setCapacite(50); 
        $manager->persist($offre3);
        
        $prixOffreEvenement3 = new PrixOffreEvenement();
        $prixOffreEvenement3->setOffre($offre3);
        $prixOffreEvenement3->setEvenement($evenement3);
        $prixOffreEvenement3->setPrix(200.00);
        $manager->persist($prixOffreEvenement3);

        $manager->flush();
    }
}
