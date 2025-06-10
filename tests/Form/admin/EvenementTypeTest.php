<?php

namespace App\Tests\Form\Admin;

use App\Form\Admin\EvenementType; // Assurez-vous que le nom du formulaire est correct
use App\Entity\Evenement; // Le formulaire est mappé à l'entité Evenement
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

// Nous n'avons PAS besoin de ces imports pour Doctrine si la catégorie est une chaîne
// use Symfony\Bridge\Doctrine\Form\Extension\DoctrineOrmExtension;
// use Doctrine\ORM\EntityManagerInterface;
// use Doctrine\ORM\EntityRepository;
// use Doctrine\ORM\QueryBuilder;
// use Doctrine\ORM\AbstractQuery;
// use Doctrine\Persistence\ObjectRepository;

class EvenementTypeTest extends TypeTestCase
{
    // Pas besoin de setUp() ici si pas de mocks Doctrine
    // protected function setUp(): void
    // {
    //     parent::setUp();
    // }

    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
            // Pas besoin de DoctrineOrmExtension ici si aucun champ EntityType
            // new DoctrineOrmExtension($this->entityManager),
        ];
    }

    public function testBuildForm(): void
    {
        // Créez une instance du formulaire EvenementType
        $form = $this->factory->create(EvenementType::class);

        // VÉRIFIEZ ET AJUSTEZ CES CHAMPS EN FONCTION DE VOTRE src/Form/Admin/EvenementType.php
        // Champs basés sur votre entité Evenement : nom, categorie, date, lieu, description
        $this->assertTrue($form->has('nom'), 'Le formulaire Evenement doit avoir un champ "nom".');
        $this->assertTrue($form->has('categorie'), 'Le formulaire Evenement doit avoir un champ "categorie".');
        $this->assertTrue($form->has('date'), 'Le formulaire Evenement doit avoir un champ "date".');
        $this->assertTrue($form->has('lieu'), 'Le formulaire Evenement doit avoir un champ "lieu".');
        $this->assertTrue($form->has('description'), 'Le formulaire Evenement doit avoir un champ "description".');
        
        // Si vous avez un champ pour la collection PrixOffreEvenement (souvent via CollectionType)
        // $this->assertTrue($form->has('prixOffreEvenements'), 'Le formulaire Evenement doit avoir un champ "prixOffreEvenements".');


        // Vérifiez le type de certains champs si nécessaire
        $nomFieldConfig = $form->get('nom')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\TextType', $nomFieldConfig->getType()->getInnerType()::class, 'Le champ "nom" doit être de type TextType.');

        $categorieFieldConfig = $form->get('categorie')->getConfig();
        // Categorie est une chaîne, donc ce peut être TextType ou ChoiceType
        $this->assertTrue(in_array($categorieFieldConfig->getType()->getInnerType()::class, ['Symfony\Component\Form\Extension\Core\Type\TextType', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType']), 'Le champ "categorie" doit être de type TextType ou ChoiceType.');

        $dateFieldConfig = $form->get('date')->getConfig();
        // Attention, votre entité est Types::DATETIME_MUTABLE, donc DateTimeType est plus probable
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\DateTimeType', $dateFieldConfig->getType()->getInnerType()::class, 'Le champ "date" doit être de type DateTimeType.');
        
        $lieuFieldConfig = $form->get('lieu')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\TextType', $lieuFieldConfig->getType()->getInnerType()::class, 'Le champ "lieu" doit être de type TextType.');
        
        $descriptionFieldConfig = $form->get('description')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\TextareaType', $descriptionFieldConfig->getType()->getInnerType()::class, 'Le champ "description" doit être de type TextareaType.');
    }

    public function testSubmitValidData(): void
    {
        // 1. Préparez les données à soumettre
        // AJUSTEZ CES DONNÉES EN FONCTION DES CHAMPS DE VOTRE src/Form/Admin/EvenementType.php
        $formData = [
            'nom' => 'Nom Test Evenement',
            'categorie' => 'Sport', // Catégorie comme une chaîne
            'date' => '2025-08-01 14:30:00', // Format string pour le formulaire DateTimeType
            'lieu' => 'Arena Paris',
            'description' => 'Description détaillée de l\'événement test.',
            // Si vous avez des champs pour la collection PrixOffreEvenement, les données seraient ici
            // 'prixOffreEvenements' => [...],
        ];

        // 2. Créez une instance de l'entité Evenement, car ce formulaire est mappé.
        $evenement = new Evenement();

        // 3. Créez le formulaire et soumettez les données
        $form = $this->factory->create(EvenementType::class, $evenement);
        $form->submit($formData);

        // 4. Vérifiez que le formulaire est soumis et valide
        $this->assertTrue($form->isSynchronized(), 'Le formulaire doit être synchronisé.');
        $this->assertTrue($form->isSubmitted(), 'Le formulaire doit être soumis.');
        $this->assertTrue($form->isValid(), 'Le formulaire doit être valide avec des données valides.');

        // 5. Vérifiez que les données ont bien été transférées à l'entité Evenement
        $this->assertEquals('Nom Test Evenement', $evenement->getNom(), 'Le nom de l\'événement ne correspond pas.');
        $this->assertEquals('Sport', $evenement->getCategorie(), 'La catégorie de l\'événement ne correspond pas.');
        // Pour les dates, l'entité attend un DateTimeInterface (votre entité est mutable), donc on peut utiliser DateTime
        $this->assertEquals(new \DateTime('2025-08-01 14:30:00'), $evenement->getDate(), 'La date de l\'événement ne correspond pas.');
        $this->assertEquals('Arena Paris', $evenement->getLieu(), 'Le lieu de l\'événement ne correspond pas.');
        $this->assertEquals('Description détaillée de l\'événement test.', $evenement->getDescription(), 'La description de l\'événement ne correspond pas.');

        // Si vous avez testé la collection PrixOffreEvenement, ajoutez des assertions ici
        // $this->assertCount(1, $evenement->getPrixOffreEvenements());
        // $this->assertInstanceOf(PrixOffreEvenement::class, $evenement->getPrixOffreEvenements()->first());
    }
}