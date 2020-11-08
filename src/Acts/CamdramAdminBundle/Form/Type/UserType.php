<?php

namespace Acts\CamdramAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Form\Type\EntitySearchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class UserType
 *
 * The form that's presented when a user is edited
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('is_email_verified', ChoiceType::class, ['label' => 'Email verified',
                'expanded' => true, 'choices' => ['Yes' => true, 'No' => false]])
            ->add('person', EntitySearchType::class, array('other_allowed' => false,
                'required' => false, 'route' => 'get_people', 'class' => 'Acts\\CamdramBundle\\Entity\\Person'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => User::class
        ));
    }
}
