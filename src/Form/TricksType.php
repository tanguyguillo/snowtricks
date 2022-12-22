<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class TricksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
        ->add('title', TextType::class, ['label' => 'Title'])
        //->add('slug')
        ->add('description', TextType::class,)
        ->add('content', TextareaType::class)
        //->add('user')
        //->add('created_at')
        //->add('active')
        ->add('Save', submitType::class)
        ;

    // $builder
    //         ->add('title', TextType::class, ['label' => 'Title'])
        ;
        // $builder
        //     ->add('title',TextType::class)
        //     ->add('slug')
        //     ->add('content', TextareaType::class)
        //     ->add('created_at')
        //     ->add('active')
        //     ->add('user')
            
        // ;

        // ->add('category', EntityType::class, [
        //     'class' => Category::class
        // ]

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // 'data_class' => TricksType::class,
        // 'data_class' => TricksType::class,

        $resolver->setDefaults([
        
        ]);
    }
}