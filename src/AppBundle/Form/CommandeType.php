<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use Symfony\Component\Form\FormBuilderInterface;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Etat;


class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('etat', ChoiceType::class, array(
                    'choices' => array(
                        Commande::EtatToVerbose(Etat::PENDING) => Etat::PENDING,    
                        Commande::EtatToVerbose(Etat::PREPARING) => Etat::PREPARING,
                        Commande::EtatToVerbose(Etat::TRANSIT) => Etat::TRANSIT,
                        Commande::EtatToVerbose(Etat::CLOSED) => Etat::CLOSED),
                    'required' => true
                ));
    }
}