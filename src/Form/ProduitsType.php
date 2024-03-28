<?php

namespace App\Form;

use App\Entity\Produits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProduitsType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Maison' => 'maison',
                    'Terrain' => 'terrain',
                    'Voiture' => 'voiture',
                ],
                'label' => 'Type',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('description')
            ->add('prix')
            ->add('labelle')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Disponible' => 'disponible',
                    'Non disponible' => 'non disponible',
                ],
                'label' => 'Statut',
                'required' => true,
            ])
            ->add('periodeGarantie', IntegerType::class, [
                'label' => 'Warranty Period (months)',
                'attr' => [
                    'placeholder' => 'Enter warranty period in months',
                    'min' => 1, // Minimum value allowed
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Event Image',
                'required' => false,
                'mapped' => false, // Do not map this field to the entity property
            ])
            ->add('localisation')
            ->add('idUtilisateur');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
