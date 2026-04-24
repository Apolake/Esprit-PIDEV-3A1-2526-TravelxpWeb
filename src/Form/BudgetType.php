<?php

namespace App\Form;

use App\Entity\Budget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BudgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'maxlength' => 180,
                    'placeholder' => 'Summer vacation plan',
                ],
            ])
            ->add('destination', TextType::class, [
                'attr' => [
                    'maxlength' => 180,
                    'placeholder' => 'Barcelona',
                ],
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('plannedAmount', NumberType::class, [
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'min' => 0.01,
                    'step' => 0.01,
                    'inputmode' => 'decimal',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Total planned budget is required.'),
                    new Assert\Positive(message: 'Total planned budget must be greater than 0.'),
                ],
            ])
            ->add('currency', TextType::class, [
                'attr' => [
                    'maxlength' => 10,
                    'placeholder' => 'USD',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Currency is required.'),
                    new Assert\Regex(pattern: '/^[A-Z]{3,10}$/', message: 'Currency must be uppercase letters.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Budget::class,
        ]);
    }
}
