<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrganisationApplicationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'text', array('label' => 'Brief Description',
                'attr' => array('placeholder' => 'e.g. "Michaelmas show applications"')))
            ->add('further_info', 'textarea', array('label' => 'Further Information'))
            ->add('deadline_date', 'date', array('widget' => 'single_text'))
            ->add('deadline_time', 'time', array('widget' => 'single_text'))
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
