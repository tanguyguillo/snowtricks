<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\Category;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * class UpdateType  // ->add('save', SubmitType::class, ['attr' => ['class' => 'save']])
 *
 */
class UpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add(
            //     'title',
            //     TextType::class,
            //     [
            //         'attr' => [
            //             'class' => 'form-control',
            //             'required' => false
            //         ]
            //     ]
            // )
            // ->add('modified_at', DateType::class)
            ->add('content', TextareaType::class)
            // ->add(
            //     'category',
            //     EntityType::class,
            //     [
            //         'class' => Category::class,
            //         'required' => false
            //     ]
            // )
            // ->add(
            //     'picture',
            //     FileType::class,
            //     [
            //         'attr' => ['accept' => "image/*",],
            //         'label' => false,
            //         'data_class' => null,
            //     ]
            // )
            // ->add(
            //     'pictures',
            //     FileType::class,
            //     [
            //         'attr' => ['accept' => "image/*",],
            //         'label' => false,
            //         'multiple' => true,
            //         'mapped' => false,
            //         'required' => false
            //     ]
            // );
        ;
    }
}
