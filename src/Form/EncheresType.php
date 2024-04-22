<?php

namespace App\Form;

use App\Entity\Encheres;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\GreaterThan;

class EncheresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une date de début.']),
                ],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une date de fin.']),
                    new GreaterThan([
                        'propertyPath' => 'parent.all[dateDebut].data',
                        'message' => 'La date de fin doit être postérieure à la date de début.',
                    ]),
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => 1,
                    'Inactive' => 0,
                ],
                'label' => 'Statut',
                'placeholder' => 'Sélectionner un statut',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un statut.']),
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('prixMin', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un prix minimum.']),
                    new Range(['min' => 0, 'minMessage' => 'Le prix minimum doit être positif.']),
                ],
            ])
            ->add('prixMax', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un prix maximum.']),
                    new Range(['min' => 0, 'minMessage' => 'Le prix maximum doit être positif.']),
                    new GreaterThan([
                        'propertyPath' => 'parent.all[prixMin].data',
                        'message' => 'Le prix maximum doit être supérieur au prix minimum.',
                    ]),
                ],
            ])
            ->add('prixActuelle', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un prix actuel.']),
                    new Range(['min' => 0, 'minMessage' => 'Le prix actuel doit être positif.']),
                ],
            ])
            ->add('nbrParticipants', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le nombre de participants.']),
                    new Range(['min' => 0, 'minMessage' => 'Le nombre de participants doit être positif.']),
                ],
            ])
            ->add('produit', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un produit.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Encheres::class,
        ]);
    }
}
