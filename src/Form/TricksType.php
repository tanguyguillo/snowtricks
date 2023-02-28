<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\FileType;

use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * class TricksType
 *
 */
class TricksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                    'class' => Category::class
                ]
            )
            ->add(
                'picture',
                FileType::class,
                [
                    'attr' => ['accept' => "image/*",],
                    'label' => false,
                    'data_class' => null,
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
        // ->add(
        //     'movie',
        //     TextType::class,
        //     [
        //         'attr' => [
        //             'class' => 'form-control',
        //         ]
        //     ]
        // );
    }

    /**
     *  function configureOptions
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
