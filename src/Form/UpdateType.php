<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Category;


use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Entity\Pictures;
use App\Entity\Tricks;
use App\Entity\Picture;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * class UpdateType 
 *
 */
class UpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'picture',
                FileType::class,
                [
                    'attr' => ['accept' => "image/*",],
                    'label' => true,
                    'data_class' => null,
                    'required' => false,
                    'mapped' => false
                ]
            )
            ->add(
                'title',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                    ]
                ]
            )
            ->add('content', TextareaType::class)
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'required' => false
                ]
            )
            ->add(
                'pictures',
                FileType::class,
                [
                    'attr' => ['accept' => "image/*",],
                    'label' => false,
                    'multiple' => true,
                    'mapped' => false,
                    'required' => false
                ]
            );
    }
}
