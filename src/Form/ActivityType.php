<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Trip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('type', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Select type',
                'choices' => [
                    'Sightseeing' => 'Sightseeing',
                    'Transport' => 'Transport',
                    'Food' => 'Food',
                    'Accommodation' => 'Accommodation',
                    'Meeting' => 'Meeting',
                    'Shopping' => 'Shopping',
                    'Adventure' => 'Adventure',
                    'Other' => 'Other',
                ],
            ])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('activityDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('startTime', TimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('endTime', TimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('locationName', TextType::class, ['required' => false])
            ->add('transportType', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Select transport',
                'choices' => [
                    'Flight' => 'Flight',
                    'Train' => 'Train',
                    'Bus' => 'Bus',
                    'Taxi' => 'Taxi',
                    'Car' => 'Car',
                    'Walking' => 'Walking',
                    'Boat' => 'Boat',
                    'Other' => 'Other',
                ],
            ])
            ->add('costAmount', NumberType::class, ['required' => false])
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
            ->add('xpEarned', IntegerType::class, ['required' => false])
            ->add('status', ChoiceType::class, [
                'choices' => array_combine(Activity::ALLOWED_STATUSES, Activity::ALLOWED_STATUSES),
                'required' => true,
            ])
            ->add('trip', EntityType::class, [
                'class' => Trip::class,
                'choice_label' => fn (Trip $trip): string => sprintf('%s (#%d)', $trip->getTripName(), $trip->getId()),
                'placeholder' => 'No trip',
                'required' => false,
                'help' => 'Select the trip this activity belongs to.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
