<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
            ->add('start_at', DateTimeType::class, [
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                ])
            ->add('repeat_until', DateType::class, array('label' => 'End', 'widget' => 'single_text'))
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
