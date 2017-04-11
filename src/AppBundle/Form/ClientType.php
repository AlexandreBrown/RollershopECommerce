<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('nom', TextType::class, array('attr' => array('placeholder' => 'Entrez votre nom')))
            ->add('prenom', TextType::class, array('label' => 'Prénom', 'attr' => array('placeholder' => 'Entrez votre prénom')))
            ->add('genre', ChoiceType::class, array(
                    'choices' => array(
                        'Femme' => 'F',    
                        'Homme' => 'M'),
                    'required' => true,
                    'expanded' => true,
                    'choices_as_values' => true
                ))
            ->add('adresse', TextType::class, array('attr' => array('placeholder' => 'Entrez votre adresse')))
            ->add('ville', TextType::class, array('attr' => array('placeholder' => 'Entrez votre ville')))
            ->add('codePostal', TextType::class, array('attr' => array('placeholder' => 'Entrez votre code postal')))
            ->add('province', ChoiceType::class, array(
                    'choices' => array(
                        'Alberta' => 'AB',    
                        'Colombie-Britannique' => 'BC',
                        'Manitoba' => 'MB',
                        'Nouveau-Brunswick' => 'NB',
                        'Terre-Neuve-et-Labrador' => 'NL',
                        'Nouvelle-Écosse' => 'NS',
                        'Territoires du Nord-Ouest' => 'NT',
                        'Nunavut' => 'NU',
                        'Ontario' => 'ON',
                        'Île-du-Prince-Édouard' => 'PE',
                        'Québec' => 'QC',
                        'Saskatchewan' => 'SK',
                        'Yukon' => 'YT'),
                    'required' => true
                ))
            ->add('telephone', TextType::class, array('attr' => array('placeholder' => 'Entrez votre numéro de téléphone')))
            ->add('btnEnregistrerChangements', SubmitType::class);
            $builder->get('codePostal')->addModelTransformer(new CallbackTransformer(
            function ($codePostal) {
                //Vers la vue
                $newCodePostal = substr($codePostal,0,3).' '.substr($codePostal,3,3);
                return $newCodePostal;
            },
            function ($codePostalView) {
                // Vers la BD (Model)
                return str_replace(" ", "", $codePostalView);
            }
            ));
            $builder->get('telephone')->addModelTransformer(new CallbackTransformer(
            function ($telephone) {
                $newTelephone = substr($telephone,0,3).'-'.substr($telephone,3,3).'-'.substr($telephone,6,4);
                //Vers la vue
                return $newTelephone;
            },
            function ($telephoneView) {
                // Vers la BD (Model)
                return str_replace("-", "", $telephoneView);
            }
            ));
    }
}