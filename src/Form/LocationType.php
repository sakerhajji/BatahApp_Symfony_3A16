<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use App\Entity\Location;
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

        $builder
            // ->add('prix', NumberType::class, [
            //     'constraints' => [
            //         new NotBlank(['message' => 'Le prix ne peut pas être vide.']),
            //         new Regex([
            //             'pattern' => '/^\d+$/',
            //             'message' => 'Le prix doit contenir uniquement des chiffres.',
            //         ]),
            //     ],
            //     'invalid_message' => 'Le prix ne peut pas être vide.',
            //     'required' => false,
            // ])
           ->add('prix', NumberType::class, [
               
                'required' => false, // Modifier à true pour le rendre obligatoire
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Maison' => 'maison',
                    'Voiture' => 'voiture',
                ],
                'placeholder' => 'Choisir un type',
                'attr' => ['class' => 'form-control'],
                'required' => false,
               
            ])
            ->add('description', TextType::class, [
                
                
                'required' => false,
            ])
            ->add('adresse', TextType::class, [
               
                'required' => false,
            ])
            ->add('disponibilite', ChoiceType::class, [
                'choices' => [
                    'Available' => true,
                    'Not Available' => false,
                ],
                'required' => false,
                'attr' => ['class' => 'form-control'],
               
            ])
            ->add('id', ChoiceType::class, [
                'label' => 'Select User',
                'choices' => array_combine(
                    array_map(function ($user) {
                        return $user->getNomutilisateur() . ' ' . $user->getPrenomutilisateur();
                    }, $users),
                    $users
                ),
                'choice_value' => function ($user = null) {
                    return $user ? $user->getId() : '';
                },
                'choice_label' => function ($user = null) {
                    return $user ? $user->getNomutilisateur() . ' ' . $user->getPrenomutilisateur() : '';
                },
                'attr' => ['class' => 'form-control'],
                'required' => false,
                
            ])
            ->add('images', FileType::class, [
                'required' => false,
                'label' => 'Images',
                'attr' => ['class' => 'form-control-file'],
                'multiple' => true, // Allow multiple file uploads
                'mapped' => false, // Pour éviter la liaison automatique avec une entité
            ]);
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
