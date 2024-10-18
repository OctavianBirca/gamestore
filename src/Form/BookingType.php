<?php

namespace App\Form;

use App\Entity\Address;

use App\Entity\Shop;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    { 
        $builder
            
            ->add('addresses', EntityType::class, [
                'label' => "Votre adresse de livraison",
                'required' => true, 
                'class' => Address::class, 
                'expanded' => true, 
                'choices' => $options['addresses'], 
                'label_html' => true
            ])

            ->add('shop', EntityType::class, [
                'class' => Shop::class,
                'required' => true,
                'label' => 'Choisissez le magasin pour récupérer votre commande',
            ])
            
            ->add('pickup_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Choisissez une date de récupération',
                'attr' => [
                    'class' => 'pickup-date', // Adăugăm o clasă pentru a-l identifica în JavaScript
                    'min' => (new \DateTime('+1 day'))->format('d-m-Y'), // Minimum o zi în viitor
                ], 
                'required' => true,

            ])
            

            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'addresses' => null,
        ]);
    }
}
