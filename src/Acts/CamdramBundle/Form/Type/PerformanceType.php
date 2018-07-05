<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Acts\CamdramBundle\Form\Type\EntitySearchType;

/**
 * Class PerformanceType
 *
 * The sub-form representing a performance - used by ShowType, once for each range of performances
 */
class PerformanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder// 'format' => 'eee d MMM y',
            ->add('start_date', DateType::class, array('label' => 'Start', 'widget' => 'single_text'))
            ->add('end_date', DateType::class, array('label' => 'End', 'widget' => 'single_text'))
            ->add('time', TimeType::class, array('label' => 'Time', 'widget' => 'single_text', 'attr' => array(
                'placeholder' => 'e.g. 19:45'
            )))
            ->add('venue', EntitySearchType::class, array(
                'route' => 'get_venues',
                'class' => 'Acts\\CamdramBundle\\Entity\\Venue',
                'required' => false,
                'text_field' => 'other_venue'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Performance'
        ));
    }
}
