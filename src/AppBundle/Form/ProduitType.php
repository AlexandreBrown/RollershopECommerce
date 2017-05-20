<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use Symfony\Component\Form\FormBuilderInterface;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('nom', TextType::class,array('label' => 'Nom'))
            ->add('prix', MoneyType::class, array('label' => 'Prix','currency' => 'CAD','attr' => array('type' => 'decimal')))
            ->add('categorie', EntityType::class, array(
                'class' => 'AppBundle:Categorie',
                'choice_label' => 'nom',
                'expanded' => false,
                'multiple' => false
                ))
            ->add('qteStock', IntegerType::class, array('attr' => array('label' => 'Qte stock')))
            ->add('qteMinimale', IntegerType::class,array('attr' => array('label' => 'Qte minimale')))
            ->add('descriptionCourte', TextareaType::class,array('attr' => array('label' => 'Description courte'),'required' => false))
            ->add('description', TextareaType::class, array('attr' => array('label' => 'Description'),'required' => false))
            ->add('btnAction', SubmitType::class);

    }
}