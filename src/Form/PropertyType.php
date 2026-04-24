<?php

namespace App\Form;

use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => '',
                'attr' => ['maxlength' => 180],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 4, 'maxlength' => 3000],
            ])
            ->add('propertyType', TextType::class, [
                'empty_data' => '',
                'label' => 'Property type',
                'attr' => ['maxlength' => 80],
            ])
            ->add('city', TextType::class, [
                'empty_data' => '',
                'attr' => ['maxlength' => 120],
            ])
            ->add('country', TextType::class, [
                'empty_data' => '',
                'attr' => ['maxlength' => 120],
            ])
            ->add('address', TextType::class, [
                'required' => false,
                'attr' => ['maxlength' => 255],
            ])
            ->add('latitude', HiddenType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->add('longitude', HiddenType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->add('pricePerNight', NumberType::class, [
                'label' => 'Price per night (USD)',
                'scale' => 2,
                'html5' => false,
                'attr' => ['min' => 0, 'step' => '0.01'],
            ])
            ->add('bedrooms', IntegerType::class, [
                'attr' => ['min' => 0],
            ])
            ->add('maxGuests', IntegerType::class, [
                'label' => 'Max guests',
                'attr' => ['min' => 1],
            ])
            ->add('images', TextType::class, [
                'required' => false,
                'label' => 'Image path/URL',
                'attr' => ['maxlength' => 255],
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Upload image',
                'constraints' => [
                    new Assert\Image(
                        maxSize: '4M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                        mimeTypesMessage: 'Please upload a valid image file (JPG, PNG, WEBP, GIF).'
                    ),
                ],
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'label' => 'Active listing',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
