<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

class CommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'content',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new Length([
                            'min' => 4,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096,
                        ]),
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
