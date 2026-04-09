<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class UserGamificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('xp', IntegerType::class, [
                'attr' => ['min' => 0],
                'constraints' => [new PositiveOrZero(message: 'XP cannot be negative.')],
            ])
            ->add('level', IntegerType::class, [
                'attr' => ['min' => 1],
                'constraints' => [new Positive(message: 'Level must be at least 1.')],
            ])
            ->add('streak', IntegerType::class, [
                'attr' => ['min' => 0],
                'constraints' => [new PositiveOrZero(message: 'Streak cannot be negative.')],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
