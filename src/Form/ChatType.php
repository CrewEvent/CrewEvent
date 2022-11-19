<?php

namespace App\Form;

use App\Entity\ChatEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'content',
                TextareaType::class,
                [
                    'attr' => ['class' => 'form-control form-control-lg', 'placeholder' => 'entrer un message']
                ]
            )
            //->add('createdAt')
            //->add('sender')
            //->add('event')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChatEvent::class,
        ]);
    }
}