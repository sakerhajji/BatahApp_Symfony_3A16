<?php

namespace App\Form;

use App\Entity\ServiceApresVente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceApresVenteTypeEdit extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    // Add your choices here
                    'voiture ' => 'voiture',
                    'terrain ' => 'terrain',
                    // Add more choices as needed
                ],
            ])
            ->add('date')
            ->add('status')
            ->add('achats')
             ->add('idPartenaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServiceApresVente::class,
        ]);
    }
}
