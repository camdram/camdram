<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PerformanceType
 *
 * The sub-form representing a performance - used by ShowType, once for each range of performances
 *
 * @package Acts\CamdramBundle\Form\Type
 */
class PerformanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder// 'format' => 'eee d MMM y',
            ->add('start_date', 'date', array('label' => 'Start', 'widget' => 'single_text'))
            ->add('end_date', 'date', array('label' => 'End', 'widget' => 'single_text'))
            ->add('time', 'time', array('label' => 'Time', 'widget' => 'single_text', 'attr' => array(
                'placeholder' => 'e.g. 19:45'
            )))
            ->add('venue', 'entity_search', array(
                'route' => 'get_venues',
                'class' => 'Acts\\CamdramBundle\\Entity\\Venue',
                'required' => false,
                'text_field' => 'other_venue'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Performance'
        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_performancetype';
    }
}
