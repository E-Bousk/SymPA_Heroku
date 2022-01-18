<?php

namespace App\Form;

use App\Entity\Annonces;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OffersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'Titre :',
                'attr' => [
                    'class' => 'mb-3 form-control rounded-1'
                ]
            ])
            ->add('content', HiddenType::class)
            ->add('categories', EntityType::class, [
                'label' => 'Catégorie :',
                'label_attr' => [
                    'class' => 'mt-3'
                ],
                'class' => Categories::class,
                'attr' => [
                    'class' => 'mb-3 form-control rounded-1'
                ]
            ])
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('Sauvegarder', SubmitType::class, [
                'attr' => [
                    'class' => 'btn shadow-1 rounded-1 small success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}
