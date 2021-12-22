<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email : ',
                'attr' => [
                    'class' => 'mb-3 form-control rounded-1'
                ]
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet du message : ',
                'attr' => [
                    'class' => 'mb-3 form-control rounded-1'
                ]
            ])
            ->add('message', CKEditorType::class, [
                'label' => 'Votre message : '
                ])
            ->add('Envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'my-3 btn shadow-1 rounded-1 small primary'
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
