<?php

namespace App\Form;

use App\Entity\SolanaContract;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SolanaContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Título del Contrato'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
            ])
            ->add('donor', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Donante',
            ])
            ->add('donorWallet', TextType::class, [
                'label' => 'Wallet del Donante',
                'attr' => ['maxlength' => 44]
            ])
            ->add('volunteer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Voluntario',
            ])
            ->add('volunteerWallet', TextType::class, [
                'label' => 'Wallet del Voluntario',
                'attr' => ['maxlength' => 44]
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Monto (SOL)',
                'scale' => 9,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SolanaContract::class,
        ]);
    }
}
