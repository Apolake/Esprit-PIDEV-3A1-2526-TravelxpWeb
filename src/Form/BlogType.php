<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message: 'Title is required.'),
                    new Assert\Length(min: 3, max: 180),
                ],
            ])
            ->add('content', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message: 'Content is required.'),
                    new Assert\Length(min: 10, max: 10000),
                ],
                'attr' => [
                    'rows' => 8,
                ],
            ])
            ->add('imageUrl', TextType::class, [
                'required' => false,
                'label' => 'Image URL (optional)',
                'constraints' => [
                    new Assert\Length(max: 500),
                    new Assert\Url(message: 'Image must be a valid URL.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
