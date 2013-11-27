<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuditionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array('widget' => 'single_text'))
            ->add('start_time', 'time', array('widget' => 'single_text'))
            ->add('end_time', 'time', array('widget' => 'single_text'))
            ->add('location')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Audition'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acts_camdrambundle_audition';
    }
}
