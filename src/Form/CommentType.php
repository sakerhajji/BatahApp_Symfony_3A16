<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;




use App\Entity\Event; // Add this line to use Event entity
use App\Entity\User; // Add this line to use user entity
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Correct use statement


class CommentType extends AbstractType
{
    /*
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire')
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'name', // Display the 'name' property of the Event entity
                'placeholder' => 'Select an event', // Optional: Display a placeholder in the dropdown
            ]);
    }
*/

    // CommentType.php

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire')
            /*
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'id', // Display the id property of the Event entity
                'placeholder' => 'Select an event', // Optional: Display a placeholder in the dropdown
                'mapped' => true, // This line excludes the 'event' field from being mapped to the form
            ])
            ->add('user', EntityType::class, [
                'class' => User::class, // Replace User::class with the actual User entity class
                'choice_label' => 'id', // Adjust the property based on your User entity
                'placeholder' => 'Select a user', // Optional: Display a placeholder in the dropdown
                'mapped' => true, // This line includes the 'user' field in the form
            ])
            */
            ;
            
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
