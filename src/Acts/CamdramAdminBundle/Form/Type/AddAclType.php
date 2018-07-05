<?php

namespace Acts\CamdramAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Acts\CamdramBundle\Form\Type\EntitySearchType;

/**
 * Class AddAclType
 *
 * The form that's presented when a user wishes to add a new entry to the ACL (access control list), i.e. permit
 * a user to edit a particular entity.
 */
class AddAclType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('entity', EntitySearchType::class, array('class' => 'Acts\\CamdramBundle\\Entity\\Entity'))
        ;
    }
}
