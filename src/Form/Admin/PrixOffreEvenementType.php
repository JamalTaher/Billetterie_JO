<?php

namespace App\Form\Admin;

use App\Entity\Offre;
use App\Entity\PrixOffreEvenement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrixOffreEvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('offre', EntityType::class, [
                'class' => Offre::class,
                'choice_label' => 'nom',
                'label' => 'Offre',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (€)',
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01',
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrixOffreEvenement::class,
        ]);
    }
}