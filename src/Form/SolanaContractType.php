<?php

namespace App\Form;

use App\Entity\SolanaContract;
use App\Form\DataTransformer\EmailToUserTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SolanaContractType extends AbstractType
{
    public function __construct(
        private EmailToUserTransformer $transformer
    ) {
    }

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
            ->add('donor', TextType::class, [
                'label' => 'Donante',
                'attr' => [
                    'class' => 'js-user-autocomplete',
                    'data-autocomplete-url' => '/api/users/search',
                    'autocomplete' => 'off',
                    'placeholder' => 'Escribe el email del donante',
                ],
                'help' => 'Escribe el email para buscar un usuario.',
                'invalid_message' => 'El usuario con este email no existe.',
            ])
            ->add('donorWallet', TextType::class, [
                'label' => 'Wallet del Donante',
                'attr' => ['maxlength' => 44]
            ])
            ->add('volunteer', TextType::class, [
                'label' => 'Voluntario',
                'attr' => [
                    'class' => 'js-user-autocomplete',
                    'data-autocomplete-url' => '/api/users/search',
                    'autocomplete' => 'off',
                    'placeholder' => 'Escribe el email del voluntario',
                ],
                'help' => 'Escribe el email para buscar un usuario.',
                'invalid_message' => 'El usuario con este email no existe.',
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

        $builder->get('donor')->addModelTransformer($this->transformer);
        $builder->get('volunteer')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SolanaContract::class,
        ]);
    }
}
