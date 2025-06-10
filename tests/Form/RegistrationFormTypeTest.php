<?php

namespace App\Tests\Form;

use App\Form\RegistrationFormType;
use App\Entity\Utilisateur; // <--- C'est ICI que le changement est crucial : Utilisateur au lieu de User
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class RegistrationFormTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function testBuildForm(): void
    {
        $form = $this->factory->create(RegistrationFormType::class);

        $this->assertTrue($form->has('email'), 'Le formulaire doit avoir un champ "email".');
        $this->assertTrue($form->has('nom'), 'Le formulaire doit avoir un champ "nom".');
        $this->assertTrue($form->has('prenom'), 'Le formulaire doit avoir un champ "prenom".');
        $this->assertTrue($form->has('agreeTerms'), 'Le formulaire doit avoir un champ "agreeTerms".');
        $this->assertTrue($form->has('plainPassword'), 'Le formulaire doit avoir un champ "plainPassword".');
        $this->assertTrue($form->get('plainPassword')->has('first'), 'Le champ plainPassword doit avoir un sous-champ "first".');
        $this->assertTrue($form->get('plainPassword')->has('second'), 'Le champ plainPassword doit avoir un sous-champ "second".');

        $emailFieldConfig = $form->get('email')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\EmailType', $emailFieldConfig->getType()->getInnerType()::class, 'Le champ "email" doit être de type EmailType.');

        $agreeTermsFieldConfig = $form->get('agreeTerms')->getConfig();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\CheckboxType', $agreeTermsFieldConfig->getType()->getInnerType()::class, 'Le champ "agreeTerms" doit être de type CheckboxType.');
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'nom' => 'Jean',
            'prenom' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'plainPassword' => [
                'first' => 'Motdepasse123!',
                'second' => 'Motdepasse123!',
            ],
            'agreeTerms' => true,
        ];

        // 2. On crée une instance de l'entité Utilisateur (celle que le formulaire est censé remplir)
        $utilisateur = new Utilisateur(); // <--- C'est ICI le changement

        // 3. On crée le formulaire en lui passant l'entité Utilisateur
        $form = $this->factory->create(RegistrationFormType::class, $utilisateur); // <--- Et ICI

        // 4. On soumet les données au formulaire
        $form->submit($formData);

        // 5. On vérifie que le formulaire est valide et soumis
        $this->assertTrue($form->isSynchronized(), 'Le formulaire doit être synchronisé.');
        $this->assertTrue($form->isSubmitted(), 'Le formulaire doit être soumis.');
        $this->assertTrue($form->isValid(), 'Le formulaire doit être valide avec des données valides.');

        // 6. On vérifie que les données ont bien été transférées à l'entité Utilisateur
        $this->assertEquals('Jean', $utilisateur->getNom(), 'Le nom de l\'utilisateur ne correspond pas.');
        $this->assertEquals('Dupont', $utilisateur->getPrenom(), 'Le prénom de l\'utilisateur ne correspond pas.');
        $this->assertEquals('jean.dupont@example.com', $utilisateur->getEmail(), 'L\'email de l\'utilisateur ne correspond pas.');

        // Pour le mot de passe, vous ne pouvez pas vérifier la valeur exacte car elle est hachée
        // dans le contrôleur ou un service. Ici, on vérifie juste qu'il n'est pas vide (s'il est mappé)
        // ou que la logique du formulaire ne le casse pas.
        // Si plainPassword n'est PAS mappé à l'entité Utilisateur (ce qui est souvent le cas pour les mots de passe bruts),
        // alors vous ne devez pas tester $utilisateur->getPassword() ici directement.
        // Si plainPassword est non mappé et traité dans le contrôleur, cette assertion serait fausse :
        // $this->assertNotNull($utilisateur->getPassword(), 'Le mot de passe de l\'utilisateur ne doit pas être null.');
        // Au lieu de ça, vous pouvez tester la valeur du champ 'plainPassword' elle-même avant qu'elle soit hachée.
        $this->assertEquals('Motdepasse123!', $form->get('plainPassword')->get('first')->getData(), 'Le premier mot de passe saisi ne correspond pas.');
    }
}