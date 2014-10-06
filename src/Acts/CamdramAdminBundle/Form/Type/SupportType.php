<?php

namespace Acts\CamdramAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SupportType
 *
 * The form that's presented when a support issue reply is created.
 *
 * @package Acts\CamdramBundle\Form\Type
 */
class SupportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('to', 'text')
            ->add('cc', 'text', array('required' => false))
            ->add('bcc', 'text', array('mapped' => false, 'required' => false))
            ->add('subject', 'text')
            ->add('body', 'textarea')
            ->add('send', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramAdminBundle\Entity\Support'
        ));
    }

    public function getName()
    {
        return 'acts_camdramadminbundle_supporttype';
    }
}
