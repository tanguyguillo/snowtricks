<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\User;

class AvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'avatar',
                FileType::class,
                [
                    'attr' => ['accept' => "image/*",],
                    'label' => false,
                    'multiple' => false,
                    'mapped' => false,
                    'required' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => User::class
            'data_class' => null
        ]);
    }
}
