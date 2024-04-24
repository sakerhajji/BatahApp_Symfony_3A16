<?php

namespace App\Form;

use App\Entity\Partenaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Finder\Finder;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PartenairesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "Le nom ne peut pas être vide"]),
                    new Assert\Length([
                        'max' => 20,
                        'maxMessage' => "Le nom ne peut pas dépasser {{ limit }} caractères"
                    ])
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'voiture' => 'voiture',
                    'maison' => 'maison',
                    'terrain' => 'terrain',
                    'livraison'=>'livraison'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => "Le type ne peut pas être vide"]),
                    new Assert\Choice([
                        'choices' => ["voiture", "maison", "terrain","livraison"],
                        'message' => "Le type doit être voiture, maison ou terrain"
                    ])
                ]
            ])
            ->add('adresse', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "L'adresse ne peut pas être vide"]),
                    new Assert\Length([
                        'max' => 20,
                        'maxMessage' => "L'adresse ne peut pas dépasser {{ limit }} caractères"
                    ])
                ]
            ])
            ->add('telephone', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "Le téléphone ne peut pas être vide"]),
                    new Assert\Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => "Le téléphone doit être composé de 8 chiffres"
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "L'email ne peut pas être vide"]),
                    new Assert\Email(['message' => "L'email doit être valide"])
                ]
            ])

            ->add('logo', FileType::class, [
                'attr' => ['class' => 'form-style', 'placeholder' => 'Votre image'],])

        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partenaires::class,
            'attr' => ['enctype' => 'multipart/form-data'],
        ]);
    }

}
