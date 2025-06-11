<?php

namespace App\Form\Admin;

use App\Entity\Offre;
use App\Entity\PrixOffreEvenement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// Importer les événements de formulaire si on veut de la logique complexe PHP
// use Symfony\Component\Form\FormEvent;
// use Symfony\Component\Form\FormEvents;

class PrixOffreEvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('offre', EntityType::class, [
                'class' => Offre::class,
                'choice_label' => function(Offre $offre) {
                    // Affiche le nom de l'offre et son prix standard pour aider l'admin
                    return $offre->getNom() . ' (' . $offre->getPrixStandard() . '€)';
                },
                'label' => 'Type d\'offre standard',
                'attr' => [
                    'class' => 'form-control prix-offre-evenement-offre-select', // Ajout d'une classe pour le JS
                ],
                'placeholder' => 'Sélectionnez un type d\'offre', // Ajout d'un placeholder
                'required' => true,
                // On ajoute un attribut data-prix-standard à chaque option via choice_attr
                'choice_attr' => function(Offre $offre, $key, $value) {
                    return ['data-prix-standard' => $offre->getPrixStandard()];
                },
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix spécifique pour cet événement (€) (laisser vide pour le prix standard)',
                'html5' => true, // Permet les flèches haut/bas et les décimales dans le navigateur
                'scale' => 2, // Gère les centimes
                'attr' => [
                    'class' => 'form-control prix-offre-evenement-prix-field', // Ajout d'une classe pour le JS
                    'placeholder' => 'Prix standard de l\'offre', // Placeholder pour guider l'utilisateur
                ],
                'required' => false, // Rendre ce champ non obligatoire
            ])
        ;

        // Si le prix n'est pas saisi, il faudra le récupérer du prixStandard de l'Offre en PHP (dans le contrôleur avant persistance)
        // ou via un FormEvent::SUBMIT pour Doctrine. Mais pour l'instant, le JS va aider l'utilisateur.
        // Laisser vide pour que l'entité PrixOffreEvenement ait un prix à NULL si non renseigné.
        // C'est dans le contrôleur ou le service de traitement que vous déciderez si NULL = prixStandard de l'Offre.
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrixOffreEvenement::class,
        ]);
    }
}