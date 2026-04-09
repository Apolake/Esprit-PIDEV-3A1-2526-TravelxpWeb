<?php

namespace App\Form;

use App\Entity\Quest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => '',
                'attr' => ['maxlength' => 140],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('goal', IntegerType::class, [
                'attr' => ['min' => 1],
            ])
            ->add('rewardXp', IntegerType::class, [
                'attr' => ['min' => 0],
                'label' => 'Reward XP',
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'label' => 'Active quest',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quest::class,
        ]);
    }
}
