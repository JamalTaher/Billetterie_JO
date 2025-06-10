<?php

namespace App\Tests\Form;

use App\Form\ChangePasswordType; // <<< CORRIGÉ : Nom exact du formulaire
// use App\Entity\Utilisateur; // Gardez cette ligne commentée ou supprimez-la si le formulaire n'est pas mappé à l'entité

use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class ChangePasswordTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function testBuildForm(): void
    {
        // Créez une instance du formulaire de changement de mot de passe
        $form = $this->factory->create(ChangePasswordType::class); // <<< CORRIGÉ

        // Vérifiez la présence des champs attendus :
        $this->assertTrue($form->has('plainPassword'), 'Le formulaire doit avoir un champ "plainPassword".');
        $this->assertTrue($form->get('plainPassword')->has('first'), 'Le champ plainPassword doit avoir un sous-champ "first".');
        $this->assertTrue($form->get('plainPassword')->has('second'), 'Le champ plainPassword doit avoir un sous-champ "second".');

        // Si vous avez un champ 'oldPassword' dans votre formulaire, ajoutez :
        // $this->assertTrue($form->has('oldPassword'), 'Le formulaire doit avoir un champ "oldPassword".');

        // Vérifiez le type des champs si nécessaire
        $passwordFieldConfig = $form->get('plainPassword')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\RepeatedType', $passwordFieldConfig->getType()->getInnerType()::class, 'Le champ "plainPassword" doit être de type RepeatedType.');
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'plainPassword' => [
                'first' => 'NouveauMotDePasse!1',
                'second' => 'NouveauMotDePasse!1',
            ],
            // Si vous avez un champ 'oldPassword', ajoutez-le ici dans les données
            // 'oldPassword' => 'AncienMotDePasse!',
        ];

        // Créez le formulaire sans lui passer d'entité, car le champ de mot de passe est probablement non mappé
        $form = $this->factory->create(ChangePasswordType::class, null); // <<< CORRIGÉ

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized(), 'Le formulaire doit être synchronisé.');
        $this->assertTrue($form->isSubmitted(), 'Le formulaire doit être soumis.');
        $this->assertTrue($form->isValid(), 'Le formulaire doit être valide avec des données valides.');

        $this->assertEquals('NouveauMotDePasse!1', $form->get('plainPassword')->get('first')->getData(), 'Le nouveau mot de passe (first) ne correspond pas.');
        $this->assertEquals('NouveauMotDePasse!1', $form->get('plainPassword')->get('second')->getData(), 'Le nouveau mot de passe (second) ne correspond pas.');
    }
}