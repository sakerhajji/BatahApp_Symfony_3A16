<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idgoogle')
            ->add('nomutilisateur')
            ->add('prenomutilisateur')
            ->add('sexe')
            ->add('datedenaissance')
            ->add('adresseemail')
            ->add('motdepasse')
            ->add('adressepostale')
            ->add('numerotelephone')
            ->add('numerocin')
            ->add('pays')
            ->add('nbrproduitachat')
            ->add('nbrproduitvendu')
            ->add('nbrproduit')
            ->add('nbrpoint')
            ->add('languepreferree')
            ->add('evaluationutilisateur')
            ->add('statutverificationcompte')
            ->add('avatar')
            ->add('dateinscription')
            ->add('role')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
