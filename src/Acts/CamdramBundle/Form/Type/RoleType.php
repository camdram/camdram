<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RoleType
 *
 * The form that's presented when a support issue reply is created.
 *
 * @package Acts\CamdramBundle\Form\Type
 */
class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('mapped' => false))
            ->add('role', 'text')
            ->add('type', 'hidden')
            ->add('send', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Role'
        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_roletype';
    }
}

