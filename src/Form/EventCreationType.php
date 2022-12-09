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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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

                    'constraints' => [
                        new NotBlank([
                            'message' => "entrer un nom s'il vous plait",
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Le nomdoit étre au moins  {{ limit }} characteres',
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
                TextareaType::class

            )
            ->add('tag', ChoiceType::class, [
                'choices'  => [
                    'le tag de votre événement' => '',
                    'Gaming' => 'gaming',
                    'Sport' => 'sport',
                    'Santé' => 'santé',
                    'Politique' => 'politique',
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'  => ' ',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => "Tu dois accépter les termes d'utilisations",
                    ]),
                ]

            ])
            //->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Event::class]);
    }
}
