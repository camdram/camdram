<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'text', array('label' => 'Brief Description',
                'attr' => array('placeholder' => 'e.g. "Applications to Direct" (your show name will be included automatically)')))
            ->add('furtherInfo', 'textarea', array('label' => 'Further Information'))
            ->add('deadlineDate', 'date', array('widget' => 'single_text', 'error_bubbling' => false))
            ->add('deadlineTime', 'time', array('widget' => 'single_text'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Application'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acts_camdrambundle_application';
    }
}
