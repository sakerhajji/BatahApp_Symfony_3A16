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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
            ->add('prix', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Le prix doit être un nombre entier.'
                    ])
                ]
            ])
            ->add('labelle', null, ['constraints' => [new Assert\NotBlank()]])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Disponible' => 'disponible',
                    'Non disponible' => 'non disponible',
                ],
                'label' => 'Statut',
                'required' => true,
            ])
            ->add('periodeGarantie', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                    new Assert\GreaterThanOrEqual(['value' => 0]),
                ],
            ])
            /*
            ->add('photo', FileType::class, [
                'label' => 'Event Image',
                'required' => false,
                'mapped' => false, // Do not map this field to the entity property
            ])
  */
            /*
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Images',
                'attr' => ['class' => 'form-control-file'],
                'multiple' => true, // Allow multiple file uploads
            ])
            */
            ->add('images', FileType::class, [
                'required' => false,
                'label' => 'Images',
                'attr' => ['class' => 'form-control-file'],
                'multiple' => true, // Allow multiple file uploads
                'mapped' => false, // Pour éviter la liaison automatique avec une entité
            ])
            ->add('video', null, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter video URL',
                ],
            ])
            ->add('localisation', null, [
                'required' => false,
                'attr' => [
                    'class' => 'google-maps-link',
                ],
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
