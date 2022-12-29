<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;

use Symfony\Component\Form\Extension\Core\Type\FileType;

use App\Entity\Category;

/**
 * class TricksType
 *  // ->add('description', TextType::class) no here it's content
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
                    'empty_data' => 'John Doe',
                ],
                ['label' => ''],
                new Length([
                    'min' => 4,
                    'minMessage' => 'Your trick name should be at least {{ limit }} characters',
                ]),
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
                'label' => false ,
                'data_class' => null
                ]
                )
            ;
    }

                    // 'multiple' => false,
                // 'required' =>  false 

    /**
     *  function configureOptions
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        // 'data_class' => TricksType::class,
        $resolver->setDefaults([]);
    }
}
