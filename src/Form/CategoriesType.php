<?php

namespace App\Form;

use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Nom :',
                'attr' => [
                    'class' => 'form-control rounded-1'
                ]
            ])
            ->add('parent', null, [
                'label' => 'Parent :',
                'label_attr' => [
                    'class' => 'mt-3'
                ],
                'attr' => [
                    'class' => 'form-control rounded-1'
                ]
            ])
            ->add('color', ColorType::class, [
                'label' => 'Couleur :',
                'label_attr' => [
                    'class' => 'mt-3'
                ],
                'attr' => [
                    'class' => 'rounded-1'
                ]
            ])
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'mt-3 btn shadow-1 rounded-1 small success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categories::class,
        ]);
    }
}
