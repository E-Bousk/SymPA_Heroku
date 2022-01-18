<?php

namespace App\Form;

use App\Entity\Regions;
use App\Entity\Annonces;
use App\Entity\Categories;
use App\Entity\Departements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

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
                'label' => 'Image(s) :',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'mb-3 btn shadow-1 rounded-1 small primary'
                ]
            ])
            ->add('regions', EntityType::class, [
                'label' => 'Région :',
                'label_attr' => [
                    'class' => 'mt-3'
                ],
                'attr' => [
                    'class' => 'mb-3 form-control rounded-1'
                ],
                'mapped' => false,
                'class' => Regions::class,
                'choice_label' => 'name',
                'placeholder' => 'Région',
                'required' => false
            ])
            ->add('departements', ChoiceType::class, [
                'label' => 'Département :',
                'label_attr' => [
                    'class' => 'mt-3 '
                ],
                'attr' => [
                    'class' => 'mb-3 form-control rounded-1'
                ],
                'placeholder' => 'Département (Choisir une région)',
                'required' => false
            ])
            ->add('Sauvegarder', SubmitType::class, [
                'attr' => [
                    'class' => 'btn shadow-1 rounded-1 small success'
                ]
            ])
        ;
        
        // Modifie le formulaire (appélé avec un « addEventListener » sur le « $builder »)
        $formModifier = function(FormInterface $form, Regions $region = null) {
            $departements = (null === $region) ? [] : $region->getDepartements();

            $form->add('departements', EntityType::class, [
                'label' => 'Départements :',
                'label_attr' => [
                    'class' => 'mt-3'
                ],
                'attr' => [
                    'class' => 'mb-3 form-control rounded-1'
                ],
                'class' => Departements::class,
                'choices' => $departements,
                'choice_label' => 'name',
                'placeholder' => 'Département (Choisir une région)',
                'required' => false
            ]);
        };

        // https://symfony.com/doc/current/form/dynamic_form_modification.html
        // Écoute l'évènement de changment de « région »
        $builder->get('regions')->addEventListener(
            // Sur l'évènement 'post_submit'
            FormEvents::POST_SUBMIT,
            // Récupère l'évènement dans « $event »
            function (FormEvent $event) use ($formModifier) {
                // Dans l'évènement, on a récupéré le formulaire qui correspond à 'region'
                // Récupère donc ses données
                $region = $event->getForm()->getData();
                // Utilise le 'formModifier'
                $formModifier($event->getForm()->getParent(), $region);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}
