<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label'=>'Votre prenom',
                'attr' => [
                    'placeholder' => 'Prenom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label'=>'Votre nom',
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('address', TextType::class, [
                'label'=>'Votre address',
            ])
            ->add('postal', TextType::class, [
                'label'=>'Votre code postal',
            ])
            ->add('city', TextType::class, [
                'label'=>'Votre Ville',
            ])
            ->add('Country', CountryType::class, [
                'label'=>'Votre pays',
                'preferred_choices' => ['FR'],
                'duplicate_preferred_choices' => false
            ])
            ->add('phone', TextType::class, [
                'label'=>'Votre numero portable',
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Enregistre", 
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])

            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
