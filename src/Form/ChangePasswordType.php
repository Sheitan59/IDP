<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'disabled' => true,
                'label' =>'Mon nom'
            ])
            ->add('prenom', TextType::class, [
                'disabled' => true,
                'label' =>'Mon prénom'
            ])
            ->add('email' , EmailType::class, [
                'disabled' => true,
                'label' =>'Mon adresse email'
            ])
            ->add('nom', TextType::class, [
                'disabled' => true,
                'label' =>'Mon Nom'
            ])

            ->add('old_password', PasswordType::class, [
                'label' =>'Mon mot de passe actuel',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre mot de passe actuel'
                ]
            ])
            ->add('new_password',RepeatedType::class,[
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'label' => 'Mon nouveau mot de pass',
                'required' => true,
                'first_options' => [
                    'label' =>'Entrez votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez saisir votre nouveau mot de passe'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez confirmer votre nouveau mot de passe'
                    ]
                ],
            ])
            ->add('submit',SubmitType::class,[
                'label' => "Mettre à jour"
            ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
