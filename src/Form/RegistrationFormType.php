<?php
/* 
    -Formulaire de type Inscription
    -
    -

    */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('nom', TextType::class, [
                'required' => true,
                'label' => 'Nom',
            ])
            ->add('prenom', TextType::class, [
                'required' => true,
                'label' => 'Prenom',
            ])
            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'Username',
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label'  => '',
                'constraints' => [
                    new IsTrue([
                        'message' => "Tu dois accépter les termes d'utilisations",
                    ]),
                ],
                'attr' => ['class' => 'form-check-input me-2']
            ])
            ->add(
                'plainPassword',
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
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
