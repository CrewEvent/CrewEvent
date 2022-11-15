<?php
/* 
    -
    -
    -

    */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Vich\UploaderBundle\Form\Type\VichImageType;

/* 
    -
    -
    -

    */

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /* 
        -
        -
        -

        */
        $builder
            ->add(
                'photoProfileFile',
                VichImageType::class,
                [
                    'imagine_pattern' => 'my_thumb',
                    'required' => false,
                    'label' => "charger votre image",
                    'allow_delete' => false,
                    'image_uri' => false,
                    'delete_label' => 'supprimer',
                    'download_uri' => false,
                ]
            )

            ->add('username')
            //->add('roles')
            ->add('email')
            ->add('prenom')
            ->add('nom')
            //->add('isVerified')
            //->add('createdAt')
            //->add('updatedAt')
            ->add('description')
            ->add('country')
            ->add('city')
            ->add('gender')
            //->add('birthday')
            ->add('language');
        // ->add(
        //     'photoProfileFile',
        //     VichImageType::class,
        //     [
        //         'required' => false,
        //         'allow_delete' => true,
        //         'label' => "Image au format JPG ou PNG",
        //         'delete_label' => 'supprimer',
        //         'download_label' => false,
        //         'download_uri' => false,
        //         'image_uri' => true,
        //         'imagine_pattern' => 'my_thumb',
        //     ]


        // );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}