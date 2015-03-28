<?php

namespace Acts\CamdramSecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\True;

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
            ->add('captcha', 'ewz_recaptcha', array(
                'attr' => array(
                    'options' => array(
                        'theme' => 'clean'
                    )
                ),
                'mapped'      => false,
                'constraints' => array(
                    new True()
                )
            ))
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
