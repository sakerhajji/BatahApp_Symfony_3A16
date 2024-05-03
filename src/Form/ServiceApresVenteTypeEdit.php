<?php

namespace App\Form;

use App\Entity\Partenaires;
use App\Entity\ServiceApresVente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceApresVenteTypeEdit extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'voiture' => 'voiture',
                    'terrain' => 'terrain',
                ],
            ])
            ->add('date')
            ->add('status')
            ->add('achats')
            ->add('idPartenaire', EntityType::class, [
                'class' => Partenaires::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un partenaire',

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServiceApresVente::class,
        ]);
    }
}
