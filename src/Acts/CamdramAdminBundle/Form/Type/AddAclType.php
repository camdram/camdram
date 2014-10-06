<?php

namespace Acts\CamdramAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AddAclType
 *
 * The form that's presented when a user wishes to add a new entry to the ACL (access control list), i.e. permit
 * a user to edit a particular entity.
 *
 * @package Acts\CamdramBundle\Form\Type
 */
class AddAclType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('entity', 'entity_search', array('class' => 'Acts\\CamdramBundle\\Entity\\Entity'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_addacltype';
    }
}
