<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class MapLocationType
 *
 * A form type representing a location on a map, or a longitude/latitude pair. It is rendered as a click-able
 * Google Map (with the help of some Javascript), which gracefully degrades to two input boxes.
 */
class MapLocationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('latitude', TextType::class, array('error_bubbling' => true))
            ->add('longitude', TextType::class, array('error_bubbling' => true));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\MapLocation',
            'compound' => true,
            'class' => 'error',
            'required' => false,
            'error_bubbling' => false,
        ));
    }
}
