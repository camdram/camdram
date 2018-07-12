<?php

namespace Acts\CamdramAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class GrantType
 *
 * A form type that presents a drop-down list of permissions that can be granted. Used by the admin tools for
 * granting permissions to users/groups
 */
class GrantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = array(
            'Owner' => 'OWNER',
            'Operator (edit, delete)' => 'OPERATOR',
            'Edit' => 'EDIT',
            'Delete' => 'DELETE',
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
        return ChoiceType::class;
    }
}
