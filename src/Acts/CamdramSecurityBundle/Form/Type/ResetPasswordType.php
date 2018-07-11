<?php

namespace Acts\CamdramSecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, array(
               'first_name'  => 'password',
               'second_name' => 'confirm',
               'first_options' => array('label' => 'New password'),
               'second_options' => array('label' => 'Confirm password'),
               'type'        => PasswordType::class,
            ))
        ;
    }
}
