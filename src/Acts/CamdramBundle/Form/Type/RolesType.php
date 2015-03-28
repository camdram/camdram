<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RoleType
 *
 * The form that's presented when when adding multiple roles simultaneously
 * to a show.
 *
 * @package Acts\CamdramBundle\Form\Type
 */
class RolesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ordering', 'choice', array(
                'choices' => array(
                    'role_first' => 'Name of role followed by the person (e.g. Sound Designer: James Dooley)',
                    'name_first' => 'Name of person followed by their role (e.g. James Dooley: Sound Designer)'),
                'expanded' => true,
                'data' => 'role_first'
                ))
            ->add('separator', 'text', array(
                'attr' => array('placeholder' => ':')
                ))
            ->add('list', 'textarea', array(
                'attr' => array(
                    'placeholder' => 'Sound Designer: James Dooley',
                    'rows' => '10',
                    )
                ))
            ->add('type', 'choice', array(
                'choices' => array(
                    'cast' => 'Cast', 
                    'prod' => 'Production Team', 
                    'band' => 'Band/Orchestra'
                    )
                ))
            ->add('Add roles', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => Null));
    }

    public function getName()
    {
        return 'acts_camdrambundle_roletype';
    }
}
