<?php

namespace App\Classe;

class State 
{
    public const STATE = [
        '2' => [
            'label' => 'Payee',
            'email_subject' => 'Votre commande a ete bien payee',
            'email_template' => 'order_state_2.html'
        ],
        '3' => [
            'label' => 'Expediee',
            'email_subject' => 'Votre commande a ete bien expediee',
            'email_template' => 'order_state_3.html'
        ],
        '4' => [
            'label' => 'Livre',
            'email_subject' => 'Votre commande a ete bien livre',
            'email_template' => 'order_state_4.html'
        ],
        '5' => [
            'label' => 'Annule',
            'email_subject' => 'Votre commande a ete annule',
            'email_template' => 'order_state_5.html'
        ],
    ];

}