<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'Choose a username',
                    'minlength' => 3,
                    'maxlength' => 50,
                ],
                'constraints' => [
                    new NotBlank(normalizer: 'trim', message: 'Username is required.'),
                    new Length(min: 3, max: 50, minMessage: 'Username must be at least {{ limit }} characters.'),
                    new Regex(pattern: '/^[a-zA-Z0-9_.-]+$/', message: 'Username can contain only letters, numbers, dots, underscores and dashes.'),
                ],
            ])
            ->add('email', EmailType::class, [
                'empty_data' => '',
                'attr' => ['placeholder' => 'you@example.com'],
                'constraints' => [
                    new NotBlank(normalizer: 'trim', message: 'Email is required.'),
                    new Email(message: 'Please enter a valid email address.'),
                ],
            ])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('bio', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Tell us a bit about yourself',
                ],
            ])
            ->add('profileImage', TextType::class, [
                'required' => false,
                'label' => 'Profile image path or URL',
                'attr' => ['placeholder' => 'uploads/image.jpg or https://...'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(message: 'You should agree to the terms to continue.'),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Password fields must match.',
                'mapped' => false,
                'first_options' => [
                    'label' => 'Password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Confirm password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'constraints' => [
                    new NotBlank(message: 'Please enter a password.'),
                    new Length(
                        min: 8,
                        minMessage: 'Your password should be at least {{ limit }} characters.',
                        max: 4096
                    ),
                    new Regex(
                        pattern: '/^(?=.*[A-Za-z])(?=.*\d).+$/',
                        message: 'Password must contain at least one letter and one number.'
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
