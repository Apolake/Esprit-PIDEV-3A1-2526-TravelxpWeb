<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('providerName', TextType::class, [
                'empty_data' => '',
                'label' => 'Provider name',
                'attr' => ['maxlength' => 140],
            ])
            ->add('serviceType', TextType::class, [
                'empty_data' => '',
                'label' => 'Service type',
                'attr' => ['maxlength' => 80],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 4, 'maxlength' => 1500],
            ])
            ->add('price', NumberType::class, [
                'scale' => 2,
                'html5' => false,
                'attr' => ['min' => 0, 'step' => '0.01'],
            ])
            ->add('isAvailable', CheckboxType::class, [
                'required' => false,
                'label' => 'Available',
            ])
            ->add('ecoFriendly', CheckboxType::class, [
                'required' => false,
                'label' => 'Eco-friendly',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
