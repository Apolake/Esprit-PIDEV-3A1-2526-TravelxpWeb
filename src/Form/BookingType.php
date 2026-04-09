<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Property;
use App\Entity\Service;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $allowStatusChange = (bool) $options['allow_status_change'];

        $builder
            ->add('property', EntityType::class, [
                'class' => Property::class,
                'choice_label' => 'title',
                'placeholder' => 'Choose a property',
                'required' => false,
                'constraints' => [
                    new Assert\NotNull(message: 'Property is required.'),
                ],
            ])
            ->add('userId', TextType::class, [
                'label' => 'User ID',
                'required' => false,
                'attr' => [
                    'inputmode' => 'numeric',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'User ID is required.'),
                    new Assert\Type(type: 'integer', message: 'User ID must be numeric.'),
                    new Assert\Positive(message: 'User ID must be greater than 0.'),
                ],
            ])
            ->add('bookingDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Booking date is required.'),
                    new Assert\GreaterThanOrEqual('today', message: 'Booking date cannot be before today.'),
                ],
            ])
            ->add('duration', TextType::class, [
                'required' => false,
                'attr' => [
                    'inputmode' => 'numeric',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Duration is required.'),
                    new Assert\Type(type: 'integer', message: 'Duration must be numeric.'),
                    new Assert\Positive(message: 'Duration must be greater than 0.'),
                ],
            ])
            ->add('totalPrice', MoneyType::class, [
                'currency' => 'USD',
                'required' => false,
                'html5' => false,
                'constraints' => [
                    new Assert\NotBlank(message: 'Total price is required.'),
                    new Assert\PositiveOrZero(message: 'Total price must be greater than or equal to 0.'),
                ],
            ])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'choice_label' => static fn (Service $service): string => sprintf(
                    '%s (%s) - $%s',
                    (string) $service->getProviderName(),
                    (string) $service->getServiceType(),
                    (string) $service->getPrice()
                ),
                'query_builder' => static fn (EntityRepository $repository) => $repository->createQueryBuilder('s')->orderBy('s.providerName', 'ASC'),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'choice_attr' => static fn (Service $service): array => $service->isAvailable() ? [] : ['disabled' => 'disabled'],
            ]);

        if ($allowStatusChange) {
            $builder->add('status', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Choose status',
                'choices' => [
                    'PENDING' => Booking::STATUS_PENDING,
                    'CONFIRMED' => Booking::STATUS_CONFIRMED,
                    'CANCELLED' => Booking::STATUS_CANCELLED,
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Status is required.'),
                    new Assert\Choice(choices: [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED, Booking::STATUS_CANCELLED]),
                ],
            ]);
        }

        $builder->get('userId')->addModelTransformer(new CallbackTransformer(
            static fn ($value): string => $value === null ? '' : (string) $value,
            static fn ($value): ?int => $value === null || trim((string) $value) === '' ? null : (int) $value
        ));

        $builder->get('duration')->addModelTransformer(new CallbackTransformer(
            static fn ($value): string => $value === null ? '' : (string) $value,
            static fn ($value): ?int => $value === null || trim((string) $value) === '' ? null : (int) $value
        ));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();
            $booking = $event->getData();
            if (!$booking instanceof Booking) {
                return;
            }

            $hasUnavailableService = false;
            foreach ($booking->getServices()->toArray() as $service) {
                if (!$service->isAvailable()) {
                    $booking->removeService($service);
                    $hasUnavailableService = true;
                }
            }

            if ($hasUnavailableService) {
                $form->get('services')->addError(new FormError('Unavailable services cannot be selected.'));
            }

            if ($booking->isCancelled()) {
                return;
            }

            if ($booking->isInPast()) {
                $form->addError(new FormError('Bookings in the past cannot be edited.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'allow_status_change' => true,
        ]);

        $resolver->setAllowedTypes('allow_status_change', 'bool');
    }
}
