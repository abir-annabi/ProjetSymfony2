<?php
// src/Form/RegistrationFormType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class RegistrationFormType extends AbstractType
{public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ]
            ])
            ->add('firstName', null, [
                'constraints' => [new NotBlank()]
            ])
            ->add('lastName', null, [
                'constraints' => [new NotBlank()]
            ])
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => true, // Ajoutez cette ligne
                'constraints' => [
                    new NotBlank(['message' => 'La date de naissance est obligatoire']),
                    new LessThanOrEqual([
                        'value' => new \DateTime('-18 years'),
                        'message' => 'Vous devez avoir au moins 18 ans'
                    ])
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new Length(['min' => 6])
                ]
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}