<?php

namespace App\Form;

use App\Entity\Trip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', ChoiceType::class, [
                'required' => false,
                'empty_data' => '',
                'placeholder' => 'No user',
                'choices' => $options['user_choices'],
                'help' => 'Optional. Select an existing user.',
            ])
            ->add('tripName', TextType::class)
            ->add('origin', TextType::class, ['required' => false])
            ->add('destination', TextType::class, ['required' => false])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('startDate', DateType::class, ['widget' => 'single_text'])
            ->add('endDate', DateType::class, ['widget' => 'single_text'])
            ->add('status', ChoiceType::class, [
                'choices' => array_combine(Trip::ALLOWED_STATUSES, Trip::ALLOWED_STATUSES),
                'required' => true,
            ])
            ->add('budgetAmount', NumberType::class, ['required' => false])
            ->add('currency', ChoiceType::class, [
                'choices' => [
                    'USD' => 'USD',
                    'EUR' => 'EUR',
                    'GBP' => 'GBP',
                    'TND' => 'TND',
                    'NGN' => 'NGN',
                    'MAD' => 'MAD',
                ],
                'required' => true,
            ])
            ->add('totalExpenses', NumberType::class, ['required' => false])
            ->add('totalXpEarned', IntegerType::class, ['required' => false])
            ->add('notes', TextareaType::class, ['required' => false])
            ->add('coverImageUrl', UrlType::class, ['required' => false])
            ->add('parentId', IntegerType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
            'user_choices' => [],
        ]);

        $resolver->setAllowedTypes('user_choices', 'array');
    }
}
