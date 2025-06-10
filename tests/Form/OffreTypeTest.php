<?php

namespace App\Tests\Form;

use App\Form\OffreType; // Assurez-vous que le nom de votre formulaire est correct
use App\Entity\Offre; // Le formulaire d'offre est probablement mappé à l'entité Offre
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class OffreTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function testBuildForm(): void
    {
        // Créez une instance du formulaire Offre
        $form = $this->factory->create(OffreType::class);

        // VÉRIFIEZ ET AJUSTEZ CES CHAMPS EN FONCTION DE VOTRE OffreType.php
        // Exemples courants pour un formulaire d'offre :
        $this->assertTrue($form->has('nom'), 'Le formulaire Offre doit avoir un champ "nom".');
        $this->assertTrue($form->has('description'), 'Le formulaire Offre doit avoir un champ "description".');
        $this->assertTrue($form->has('prix_initial'), 'Le formulaire Offre doit avoir un champ "prix_initial".');
        // Ajoutez d'autres champs que votre OffreType.php contient
        // $this->assertTrue($form->has('duree'), 'Le formulaire Offre doit avoir un champ "duree".');
        // $this->assertTrue($form->has('categorie'), 'Le formulaire Offre doit avoir un champ "categorie".'); // Si c'est un champ EntityType ou ChoiceType

        // Vérifiez le type de certains champs si nécessaire
        $nomFieldConfig = $form->get('nom')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\TextType', $nomFieldConfig->getType()->getInnerType()::class, 'Le champ "nom" doit être de type TextType.');

        $prixInitialFieldConfig = $form->get('prix_initial')->getConfig();
        // Le type peut être NumberType, MoneyType, ou TextType selon votre configuration
        $this->assertTrue(in_array($prixInitialFieldConfig->getType()->getInnerType()::class, ['Symfony\Component\Form\Extension\Core\Type\NumberType', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', 'Symfony\Component\Form\Extension\Core\Type\TextType']), 'Le champ "prix_initial" doit être un type numérique/monétaire ou textuel.');

        // Si vous avez un champ de type Textarea
        // $descriptionFieldConfig = $form->get('description')->getConfig();
        // $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\TextareaType', $descriptionFieldConfig->getType()->getInnerType()::class, 'Le champ "description" doit être de type TextareaType.');
    }

    public function testSubmitValidData(): void
    {
        // 1. Préparez les données à soumettre
        // AJUSTEZ CES DONNÉES EN FONCTION DES CHAMPS DE VOTRE OffreType.php
        $formData = [
            'nom' => 'Offre Standard JO',
            'description' => 'Description de l\'offre standard.',
            'prix_initial' => 150.00,
            // Ajoutez les données pour les autres champs de votre formulaire OffreType
            // 'duree' => 'Journée',
            // 'categorie' => 1, // Si c'est l'ID d'une entité Catégorie
        ];

        // 2. Créez une instance de l'entité Offre, car ce formulaire est très probablement mappé.
        $offre = new Offre();

        // 3. Créez le formulaire et soumettez les données
        $form = $this->factory->create(OffreType::class, $offre);
        $form->submit($formData);

        // 4. Vérifiez que le formulaire est soumis et valide
        $this->assertTrue($form->isSynchronized(), 'Le formulaire doit être synchronisé.');
        $this->assertTrue($form->isSubmitted(), 'Le formulaire doit être soumis.');
        $this->assertTrue($form->isValid(), 'Le formulaire doit être valide avec des données valides.');

        // 5. Vérifiez que les données ont bien été transférées à l'entité Offre
        $this->assertEquals('Offre Standard JO', $offre->getNom(), 'Le nom de l\'offre ne correspond pas.');
        $this->assertEquals('Description de l\'offre standard.', $offre->getDescription(), 'La description de l\'offre ne correspond pas.');
        $this->assertEquals(150.00, $offre->getPrixInitial(), 'Le prix initial de l\'offre ne correspond pas.');
        // Ajoutez ici les assertions pour les autres propriétés de l'entité Offre mises à jour par le formulaire
    }
}