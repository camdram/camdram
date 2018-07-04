<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SocietyType
 *
 * The form that's presented when a user adds/edits a society
 */
class SocietyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* Short names must be included to generate URL slugs. */
        $builder
            ->add('name')
            ->add('short_name', null, array('required' => true))
            ->add('description')
            ->add('college', 'college')
            ->add('facebook_id')
            ->add('twitter_id')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Society'
        ));
    }

    public function getName()
    {
        return 'society';
    }
}
