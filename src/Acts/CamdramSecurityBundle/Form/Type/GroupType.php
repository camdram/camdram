<?php

namespace Acts\CamdramSecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('short_name')
            ->add('menu_name')
            ->add('users', 'entity_collection', array('route' => 'get_users', 'class' => 'Acts\\CamdramBundle\\Entity\\User'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramSecurityBundle\Entity\Group'
        ));
    }

    public function getName()
    {
        return 'acts_camdramsecuritybundle_grouptype';
    }
}
