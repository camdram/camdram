<?php

namespace Acts\CamdramSecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('password', 'repeated', array(
               'first_name'  => 'password',
               'second_name' => 'confirm',
               'second_options' => array('label' => 'Confirm password'),
               'type'        => 'password',
            ))
            ->add('occupation', 'occupation')
            ->add('graduation', 'graduation_year', array('required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramSecurityBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_usertype';
    }
}
