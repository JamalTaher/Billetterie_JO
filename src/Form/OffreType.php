<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'offre (ex: Tarif Solo, Tarif Duo)', 
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le nom de l\'offre.']),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacité (nombre de personnes)',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer la capacité.']),
                    new Positive(['message' => 'La capacité doit être positive.']),
                ],
            ])
            
            ->add('prixStandard', NumberType::class, [ 
                'label' => 'Prix standard de l\'offre (€)', 
                'html5' => true, 
                'scale' => 2, 
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le prix standard.']),
                    new Positive(['message' => 'Le prix standard doit être positif.']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}