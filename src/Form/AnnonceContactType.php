<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'annonce :',
                'disabled' => true,
                'attr' => [
                    'class' => 'mb-3 form-control rounded-1'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email :',
                'attr' => [
                    'class' => 'mb-3 form-control rounded-1'
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message :',
                'attr' => [
                    'class' => 'mb-3 form-control rounded-1'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
