<?php

namespace App\Tests\Form;

use App\Form\LoginFormType; // Assurez-vous que le nom de votre LoginFormType est correct
use Symfony\Component\Form\Test\TypeTestCase;
// Pour un formulaire de connexion, vous n'avez généralement pas besoin du ValidatorExtension
// car les champs ne sont pas directement mappés à une entité avec des contraintes de validation Doctrine.
// Les validations sont souvent gérées par le composant de sécurité de Symfony.
// Vous pouvez l'omettre si vous ne voyez pas d'erreurs, ou l'inclure si vous avez des contraintes @Assert
// directement sur les champs du formulaire. Pour l'instant, on va le laisser au cas où.
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;


class LoginFormTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        // Peut être omis si votre LoginFormType n'a pas de contraintes de validation directes
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function testBuildForm(): void
    {
        // On crée une instance du formulaire de connexion
        $form = $this->factory->create(LoginFormType::class);

        // Vérifiez la présence des champs attendus pour un formulaire de connexion :
        // Généralement 'email' ou '_username' et 'password' ou '_password'
        $this->assertTrue($form->has('email'), 'Le formulaire de connexion doit avoir un champ "email".');
        $this->assertTrue($form->has('password'), 'Le formulaire de connexion doit avoir un champ "password".');
        // Ajoutez d'autres assertions si votre formulaire de connexion a d'autres champs (par exemple, un champ CSRF si non géré automatiquement)

        // Vous pouvez aussi vérifier le type des champs si nécessaire, comme fait pour RegistrationFormTypeTest
        $emailFieldConfig = $form->get('email')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\EmailType', $emailFieldConfig->getType()->getInnerType()::class, 'Le champ "email" doit être de type EmailType.');

        $passwordFieldConfig = $form->get('password')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\PasswordType', $passwordFieldConfig->getType()->getInnerType()::class, 'Le champ "password" doit être de type PasswordType.');

    }

    public function testSubmitValidData(): void
    {
        // Pour un formulaire de connexion, les données ne sont généralement pas mappées à une entité.
        // On vérifie juste que le formulaire peut être soumis avec des données.
        $formData = [
            'email' => 'test@example.com',
            'password' => 'motdepasse',
        ];

        // On crée le formulaire sans lui passer d'entité, car il n'est pas mappé
        $form = $this->factory->create(LoginFormType::class);

        // Soumettre les données
        $form->submit($formData);

        // Vérifier que le formulaire est soumis et valide (au niveau de la soumission de données, pas de la validation de connexion réelle)
        $this->assertTrue($form->isSynchronized(), 'Le formulaire doit être synchronisé.');
        $this->assertTrue($form->isSubmitted(), 'Le formulaire doit être soumis.');
        $this->assertTrue($form->isValid(), 'Le formulaire doit être valide avec des données valides.'); // Valide dans le sens où les champs ont été remplis correctement

        // Vérifier que les données soumises peuvent être récupérées
        $this->assertEquals('test@example.com', $form->get('email')->getData(), 'L\'email soumis ne correspond pas.');
        $this->assertEquals('motdepasse', $form->get('password')->getData(), 'Le mot de passe soumis ne correspond pas.');
    }
}