<?php

namespace Acts\CamdramAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Yaml\Yaml;

/**
 * Class GrantType
 *
 * A form type that presents a drop-down list of permissions that can be granted. Used by the admin tools for
 * granting permissions to users/groups
 *
 * @package Acts\CamdramBundle\Form\Type
 */
class GrantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = array(
            'OWNER' => 'Owner',
            'OPERATOR' => 'Operator (edit, delete)',
            'EDIT' => 'Edit',
            'DELETE' => 'Delete',
        );

        $resolver->setDefaults(array(
            'choices' => $choices,
            'label' => 'Permissions',
            'exapnded' => true,
            'data' => 'EDIT',
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'grant_type';
    }
}
