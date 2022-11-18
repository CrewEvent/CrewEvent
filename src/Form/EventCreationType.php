<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EventCreationType extends AbstractType
{
    /*
    Formulaire création d'événement
    */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'constraints' => [
                        new NotBlank([
                            'message' => "entrer un nom s'il vous plait",
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Le nomdoit étre au moins  {{ limit }} characteres',
                            'max' => 4096
                        ])

                    ]
                ]

            )
            // ->add(
            //     'imageFile',
            //     VichImageType::class,
            //     [
            //         'imagine_pattern' => 'my_thumb',
            //         'required' => false,
            //         'label' => "Donner une image à votre événement",
            //         'allow_delete' => false,
            //         'image_uri' => false,
            //         'delete_label' => 'supprimer',
            //         'download_uri' => false,
            //     ]

            // )
            //->add('createdAt')
            //->add('updatedAt')
            ->add(
                'description',
                TextareaType::class,
                [
                    'attr' => ['class' => 'form-control']

                ]

            )
            ->add(
                'tag',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control']

                ]
            )
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => "Tu dois accépter les termes d'utilisations",
                    ]),
                ],
                'attr' => ['class' => 'form-check-input me-2']
            ])
            //->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Event::class]);
    }
}