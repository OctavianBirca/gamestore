<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 7,
                        'max' => 35
                    ])
                ],
                'first_options'  => [
                    'label' => 'Votre mot de passe',
                    'attr' => [
                        'placeholder' => "Choisissez votre nouveau mot de passe"
                    ], 
                    'hash_property_path' => 'password'
                ],
                'second_options' => [
                    'label' => 'Confirme votre mot de passe',
                    'attr' => [
                        'placeholder' => "Confirmez votre mot de passe"
                    ]
                ],
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider", 
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            
        ]);
    }
}
