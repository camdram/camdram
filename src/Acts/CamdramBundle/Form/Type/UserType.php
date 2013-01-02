<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('person', 'entity_search', array('route' => 'get_people', 'class' => 'Acts\\CamdramBundle\\Entity\\Person'))
            ->add('occupation')
            ->add('graduation')
            ->add('groups', 'entity_collection', array('route' => 'get_groups', 'class' => 'Acts\\CamdramSecurityBundle\\Entity\\Group'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_usertype';
    }
}
