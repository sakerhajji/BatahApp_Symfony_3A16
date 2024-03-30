<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use App\Entity\Utilisateur;
use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Repository\UtilisateurRepository;

class LocationType extends AbstractType

{
    private $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $users = $this->utilisateurRepository->findAllUsers();
        $userChoices = [];

        foreach ($users as $user) {
            $userChoices[$user->getNomutilisateur() . ' ' . $user->getPrenomutilisateur()] = $user->getId();
        }

        $builder
            ->add('prix')
             ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Maison' => 'maison',
                    'Voiture' => 'voiture',
                ],
                'placeholder' => 'Choisir un type',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('description')
            ->add('adresse')
            ->add('disponibilite', ChoiceType::class, [
                'choices' => [
                    'Available' => true,
                    'Not Available' => false,
                ],
            ])
            ->add('id', ChoiceType::class, [
                'label' => 'Select User',
                'choices' => array_combine(
                    array_map(function (Utilisateur $user) {
                        return $user->getNomutilisateur() . ' ' . $user->getPrenomutilisateur();
                    }, $users),
                    $users
                ),
                'choice_value' => function (Utilisateur $user = null) {
                    return $user ? $user->getId() : '';
                },
                'choice_label' => function (Utilisateur $user = null) {
                    return $user ? $user->getNomutilisateur() . ' ' . $user->getPrenomutilisateur() : '';
                },
                'attr' => ['class' => 'form-control'],
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Image',
                // Add more options here as needed
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
