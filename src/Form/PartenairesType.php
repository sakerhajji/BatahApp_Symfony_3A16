<?php

namespace App\Form;

use App\Entity\Partenaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

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
                    'terrain' => 'terrain'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => "Le type ne peut pas être vide"]),
                    new Assert\Choice([
                        'choices' => ["voiture", "maison", "terrain"],
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
            ->add('logo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partenaires::class,
        ]);
    }
}
