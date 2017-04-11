<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class,array(
            'label' => 'Mot de passe'
            ))
            ->add('newPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent être identiques',
            'required' => true,
            'first_options'  => array('label' => 'Nouveau mot de passe'),
            'second_options' => array('label' => 'Confirmation du mot de passe')))
            ->add('btnSendFormChangePwd', SubmitType::class);
    }

    public function getName()
    {
        return 'change_passwd';
    }
}