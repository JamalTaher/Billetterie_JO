<?php

namespace App\Form;

use App\Entity\Evenement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFilterType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $this->entityManager->getRepository(Evenement::class)
            ->createQueryBuilder('e')
            ->select('DISTINCT e.categorie')
            ->orderBy('e.categorie')
            ->getQuery()
            ->getScalarResult();

        $choices = ['Toutes les catégories' => ''];
        foreach ($categories as $category) {
            $choices[$category['categorie']] = $category['categorie'];
        }

        $builder
            ->add('categorie', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'Filtrer par catégorie :',
                'required' => false,
                'placeholder' => false,
            ])
            // Nous retirons l'ajout explicite du bouton submit
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET', // Important pour que les filtres apparaissent dans l'URL
        ]);
    }
}