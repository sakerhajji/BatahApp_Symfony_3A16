<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CalculatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vehiclePrice', NumberType::class, [
                'label' => 'Vehicle Price (TND)',
                'attr' => [
                    'placeholder' => 'Enter the price of the vehicle',
                ],
            ])
            ->add('interestRate', NumberType::class, [
                'label' => 'Interest Rate (%)',
                'attr' => [
                    'placeholder' => 'Enter the interest rate in percentage',
                ],
            ])
            ->add('period', NumberType::class, [
                'label' => 'Period (months)',
                'attr' => [
                    'placeholder' => 'Enter the period in months',
                ],
            ])
            ->add('downPayment', NumberType::class, [
                'label' => 'Down Payment (TND)',
                'attr' => [
                    'placeholder' => 'Enter the down payment amount',
                ],
            ])
            ->add('calculate', SubmitType::class, [
                'label' => 'Calculate',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
