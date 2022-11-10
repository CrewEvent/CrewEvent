<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'currentPassword',
                PasswordType::class,
                [
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(['message' => 'veuillez entrer un mot de passe']),
                        new UserPassword(["message" => "le message actuel n'est pas valide"])
                    ],
                    'attr' => ['placeholder' => 'votre mot de passe actuel', 'class' => 'mb-2'],
                    'label' => ' '
                ]

            )
            ->add(
                'newPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les deux mots de passe doivent correspondrent',
                    'required' => true,
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'entrer un mot de passe s il vous plait',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096
                        ])

                    ],
                    'first_options'  => ['attr' => ['placeholder' => 'nouveau mot de passe', 'class' => 'mb-2'], 'label' => ' '],
                    'second_options' => ['attr' => ['placeholder' => 'confirmer le nouveau mot de passe', 'class' => 'mb-3'], 'label' => ' ']

                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}