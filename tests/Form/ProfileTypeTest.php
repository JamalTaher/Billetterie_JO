<?php

namespace App\Tests\Form;

use App\Form\ProfileType; // Assurez-vous que le nom de votre formulaire est correct
use App\Entity\Utilisateur; // Le formulaire de profil est presque toujours mappé à l'entité Utilisateur
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class ProfileTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function testBuildForm(): void
    {
        // Créez une instance du formulaire de profil
        $form = $this->factory->create(ProfileType::class);

        // Vérifiez la présence des champs attendus dans un formulaire de profil :
        // Généralement 'nom', 'prenom', 'email' et d'autres champs liés au profil
        $this->assertTrue($form->has('nom'), 'Le formulaire doit avoir un champ "nom".');
        $this->assertTrue($form->has('prenom'), 'Le formulaire doit avoir un champ "prenom".');
        $this->assertTrue($form->has('email'), 'Le formulaire doit avoir un champ "email".');
        // Ajoutez ici d'autres assertions pour les champs spécifiques à votre formulaire ProfileType
        // Par exemple: $this->assertTrue($form->has('adresse'), 'Le formulaire doit avoir un champ "adresse".');

        // Vérifiez le type de certains champs si nécessaire
        $emailFieldConfig = $form->get('email')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\EmailType', $emailFieldConfig->getType()->getInnerType()::class, 'Le champ "email" doit être de type EmailType.');
        $nameFieldConfig = $form->get('nom')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\TextType', $nameFieldConfig->getType()->getInnerType()::class, 'Le champ "nom" doit être de type TextType.');
    }

    public function testSubmitValidData(): void
    {
        // 1. Préparez les données à soumettre
        $formData = [
            'nom' => 'NouveauNom',
            'prenom' => 'NouveauPrenom',
            'email' => 'nouveau.email@example.com',
            // Ajoutez ici les données pour les autres champs de votre formulaire de profil
        ];

        // 2. Créez une instance de l'entité Utilisateur, car ce formulaire est très probablement mappé.
        $utilisateur = new Utilisateur();
        // Vous pouvez pré-remplir l'utilisateur si c'est pertinent pour votre logique de test
        // $utilisateur->setNom('AncienNom');
        // $utilisateur->setPrenom('AncienPrenom');
        // $utilisateur->setEmail('ancien.email@example.com');

        // 3. Créez le formulaire et soumettez les données
        $form = $this->factory->create(ProfileType::class, $utilisateur);
        $form->submit($formData);

        // 4. Vérifiez que le formulaire est soumis et valide
        $this->assertTrue($form->isSynchronized(), 'Le formulaire doit être synchronisé.');
        $this->assertTrue($form->isSubmitted(), 'Le formulaire doit être soumis.');
        $this->assertTrue($form->isValid(), 'Le formulaire doit être valide avec des données valides.');

        // 5. Vérifiez que les données ont bien été transférées à l'entité Utilisateur
        $this->assertEquals('NouveauNom', $utilisateur->getNom(), 'Le nom de l\'utilisateur ne correspond pas après soumission.');
        $this->assertEquals('NouveauPrenom', $utilisateur->getPrenom(), 'Le prénom de l\'utilisateur ne correspond pas après soumission.');
        $this->assertEquals('nouveau.email@example.com', $utilisateur->getEmail(), 'L\'email de l\'utilisateur ne correspond pas après soumission.');
        // Ajoutez ici les assertions pour les autres propriétés de l'entité Utilisateur mises à jour par le formulaire
    }
}