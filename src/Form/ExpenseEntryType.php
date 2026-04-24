<?php

namespace App\Form;

use App\Entity\ExpenseEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ExpenseEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'maxlength' => 180,
                    'placeholder' => 'Airport taxi',
                ],
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Transport' => ExpenseEntry::CATEGORY_TRANSPORT,
                    'Hotel' => ExpenseEntry::CATEGORY_HOTEL,
                    'Food' => ExpenseEntry::CATEGORY_FOOD,
                    'Activities' => ExpenseEntry::CATEGORY_ACTIVITIES,
                    'Shopping' => ExpenseEntry::CATEGORY_SHOPPING,
                    'Misc' => ExpenseEntry::CATEGORY_MISC,
                ],
            ])
            ->add('amount', NumberType::class, [
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'min' => 0.01,
                    'step' => 0.01,
                    'inputmode' => 'decimal',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Amount is required.'),
                    new Assert\Positive(message: 'Amount must be greater than 0.'),
                ],
            ])
            ->add('expenseDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('note', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Optional note',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExpenseEntry::class,
        ]);
    }
}
