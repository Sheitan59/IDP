<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class EnregistrementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class,[
                'label' => 'Votre Prenom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre prénom'
                ]
            ])

            ->add('nom',TextType::class,[
                'label' => 'Votre Nom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre nom'
                ]
            ])

            ->add('email',RepeatedType::class,[
                'type' => EmailType::class,
                'invalid_message' => 'Les emails doivent être identique',
                'label' => 'Votre Email',
                'required' => true,
                'first_options' => [
                    'label' => 'Entrez votre email',
                    'attr' => [
                        'placeholder' => 'Veuillez saisir votre email'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmez votre email',
                    'attr' => [
                        'placeholder' => 'Veuillez confirmer votre email'
                    ]
                ],
            ])
            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'label' => 'Votre Mot de pass',
                'required' => true,
                'first_options' => [
                    'label' =>'Entrez votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez saisir votre mot de passe'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez confirmer votre mot de passe'
                    ]
                ],
            ])
            ->add('submit',SubmitType::class,[
                'label' => "S'inscrire"
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
