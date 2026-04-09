<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $passwordRequired = (bool) $options['password_required'];

        $builder
            ->add('username', TextType::class, [
                'empty_data' => '',
                'attr' => [
                    'minlength' => 3,
                    'maxlength' => 50,
                ],
                'constraints' => [
                    new NotBlank(normalizer: 'trim', message: 'Username is required.'),
                    new Length(min: 3, max: 50, minMessage: 'Username must be at least {{ limit }} characters.'),
                    new Regex(pattern: '/^[a-zA-Z0-9_.-]+$/', message: 'Username can contain only letters, numbers, dots, underscores and dashes.'),
                ],
            ])
            ->add('email', TextType::class, [
                'empty_data' => '',
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
                'attr' => ['rows' => 4],
            ])
            ->add('profileImage', TextType::class, [
                'required' => false,
                'label' => 'Profile image path or URL',
            ])
            ->add('role', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'data' => $options['role'],
                'placeholder' => false,
                'label' => 'Account role',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => $passwordRequired,
                'invalid_message' => 'Password fields must match.',
                'first_options' => [
                    'label' => 'Password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Confirm password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'constraints' => $passwordRequired ? [
                    new NotBlank(message: 'Please provide a password.'),
                    new Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters.'),
                    new Regex(
                        pattern: '/^(?=.*[A-Za-z])(?=.*\d).+$/',
                        message: 'Password must contain at least one letter and one number.'
                    ),
                ] : [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'password_required' => false,
            'role' => 'ROLE_USER',
        ]);
    }
}
