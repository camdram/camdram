<?php

namespace Acts\CamdramSecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current_password', 'password', [
                'mapped' => false,
                'constraints' => new UserPassword()
            ])
            ->add('password', 'repeated', [
               'first_name'  => 'password',
               'second_name' => 'confirm',
               'first_options' => array('label' => 'New password'),
               'second_options' => array('label' => 'Confirm password'),
               'type'        => 'password',
            ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_usertype';
    }
}
