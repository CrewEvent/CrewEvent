<?php
/* 
    -Formulaire de type changement de mot de passe
    -cmd: symfony console make:form
    */

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
            //Input mot de passe actuel
            ->add(
                'currentPassword',
                PasswordType::class, //type mot de passe
                [
                    'label' => 'mot de passe actuel',
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(['message' => 'veuillez entrer un mot de passe']), //Contraintes pas vide
                        new UserPassword(["message" => "le message actuel n'est pas valide"]) //Contraintes de type mot de passe de l'utilisateur
                    ],
                ]

            )
            ->add(
                'newPassword',
                RepeatedType::class, //type répétition
                [
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'required' => true,
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'entrer un mot de passe s il vous plait',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096
                        ])
                    ],
                    'first_options'  => [
                        'label' => 'Mot de passe'
                    ],
                    'second_options' => array('label' => 'Confirmer le mot de passe'),
                ]
            )

          ;
    }
    /* 
    -
    -
    -

    */

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}