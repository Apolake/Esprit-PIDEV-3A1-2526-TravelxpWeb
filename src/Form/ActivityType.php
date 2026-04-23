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
            ->add('imageUrl', TextType::class, [
                'required' => false,
                'label' => 'Image URL',
                'attr' => [
                    'placeholder' => 'https://example.com/activity.jpg or images/activities/beach.jpg',
                ],
            ])
            ->add('activityDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('startTime', TimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('endTime', TimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('locationName', TextType::class, ['required' => false])
            ->add('locationLatitude', NumberType::class, [
                'required' => false,
                'label' => 'Location latitude',
                'scale' => 6,
            ])
            ->add('locationLongitude', NumberType::class, [
                'required' => false,
                'label' => 'Location longitude',
                'scale' => 6,
            ])
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
            ->add('totalCapacity', IntegerType::class, [
                'required' => true,
                'label' => 'Total capacity',
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
                'choice_label' => fn (Trip $trip): string => sprintf('%s (#%d)', (string) $trip->getTripName(), (int) $trip->getId()),
                'placeholder' => 'No trip',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
