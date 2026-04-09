<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\Property;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
            ->add('title', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message: 'Title is required.'),
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('discountPercentage', NumberType::class, [
                'required' => false,
                'scale' => 2,
                'html5' => false,
                'constraints' => [
                    new Assert\NotBlank(message: 'Discount percentage is required.'),
                    new Assert\Range(min: 1, max: 100, notInRangeMessage: 'Discount percentage must be between {{ min }} and {{ max }}.'),
                ],
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Start date is required.'),
                ],
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'End date is required.'),
                ],
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'label' => 'Active',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
            'constraints' => [
                new Assert\Callback(function (mixed $offer, ExecutionContextInterface $context): void {
                    if (!$offer instanceof Offer) {
                        return;
                    }

                    $startDate = $offer->getStartDate();
                    $endDate = $offer->getEndDate();

                    if ($startDate !== null && $endDate !== null && $endDate < $startDate) {
                        $context
                            ->buildViolation('End date must be after or equal to start date.')
                            ->atPath('endDate')
                            ->addViolation();
                    }
                }),
            ],
        ]);
    }
}
