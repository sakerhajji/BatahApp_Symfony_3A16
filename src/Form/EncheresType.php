<?php

namespace App\Form;

use App\Entity\Encheres;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EncheresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text', // Afficher en tant qu'entrée texte unique
                'format' => 'yyyy-MM-dd', // Définir le format de date souhaité
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text', // Afficher en tant qu'entrée texte unique
                'format' => 'yyyy-MM-dd', // Définir le format de date souhaité
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => 1,
                    'Inactive' => 0,
                ],
                'label' => 'Status',
                'placeholder' => 'Select status',
                'required' => true, // or false depending on your requirement
                'attr' => [
                    'class' => 'form-control', // Add any additional classes here
                ],
            ])
            ->add('prixMin')
            ->add('prixMax')
            ->add('prixActuelle')
            ->add('nbrParticipants')
            ->add('produit');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Encheres::class,
        ]);
    }
}
