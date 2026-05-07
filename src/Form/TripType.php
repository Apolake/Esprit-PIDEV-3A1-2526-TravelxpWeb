<?php

namespace App\Form;

use App\Entity\Trip;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', ChoiceType::class, [
                'required' => false,
                'label' => 'Owner user (optional)',
                'placeholder' => 'No owner',
                'choices' => $this->buildOwnerChoices(),
            ])
            ->add('tripName', TextType::class)
            ->add('origin', TextType::class, ['required' => false])
            ->add('destination', TextType::class, ['required' => false])
            ->add('destinationLatitude', HiddenType::class, [
                'required' => false,
            ])
            ->add('destinationLongitude', HiddenType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('startDate', DateType::class, ['widget' => 'single_text'])
            ->add('endDate', DateType::class, ['widget' => 'single_text'])
            ->add('status', ChoiceType::class, [
                'choices' => array_combine(Trip::ALLOWED_STATUSES, Trip::ALLOWED_STATUSES),
                'required' => true,
            ])
            ->add('totalCapacity', IntegerType::class, [
                'required' => true,
                'label' => 'Total capacity',
            ])
            ->add('budgetAmount', NumberType::class, ['required' => false])
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
            ->add('totalExpenses', NumberType::class, ['required' => false])
            ->add('totalXpEarned', IntegerType::class, ['required' => false])
            ->add('notes', TextareaType::class, ['required' => false])
            ->add('coverImageUrl', UrlType::class, ['required' => false])
            ->add('parentId', IntegerType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function buildOwnerChoices(): array
    {
        $choices = [];
        foreach ($this->userRepository->findBy([], ['id' => 'ASC']) as $user) {
            if (!$user instanceof User || null === $user->getId()) {
                continue;
            }

            $label = sprintf('%s (%s) [#%d]', $user->getUsername(), $user->getEmail(), $user->getId());
            $choices[$label] = $user->getId();
        }

        return $choices;
    }
}
