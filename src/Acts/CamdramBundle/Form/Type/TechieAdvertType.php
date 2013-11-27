<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TechieAdvertType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('positions', 'textarea', array('label' => 'Vacant positions (one per line)'))
            ->add('contact', 'text', array('label' => 'Contact details'))
            ->add('deadline', 'checkbox', array('label' => 'Include a deadline for applications', 'required' => false))
            ->add('expiry', 'date', array('label' => 'Deadline date', 'widget' => 'single_text'))
            ->add('deadline_time', 'time', array('label' => 'Deadline time', 'widget' => 'single_text'))
            ->add('tech_extra', 'textarea', array('label' => 'Further information that might be useful to people applying for this/these positions'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\TechieAdvert'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acts_camdrambundle_techieadvert';
    }
}
