<?php

namespace Acts\CamdramSecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current_password', PasswordType::class, [
                'mapped' => false,
                'constraints' => new UserPassword()
            ])
            ->add('password', RepeatedType::class, [
               'first_name'  => 'password',
               'second_name' => 'confirm',
               'first_options' => array('label' => 'New password'),
               'second_options' => array('label' => 'Confirm password'),
               'type'        => 'password',
            ]);
    }
}
