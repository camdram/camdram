<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Ivory\GoogleMap\Map;

/**
 * Class MapLocationType
 *
 * A form type representing a location on a map, or a longitude/latitude pair. It is rendered as a click-able
 * Google Map (with the help of some Javascript), which gracefully degrades to two input boxes.
 */
class MapLocationType extends AbstractType
{
    private $map;

    public function __construct(array $center)
    {
        $map = new Map;
        $map->setCenter($center[0], $center[1], true);
        $map->setMapOption('zoom', 14);
        $map->setStylesheetOptions(array('width' => '100%', 'height' => '100%'));

        $this->map = $map;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('latitude', 'text', array('error_bubbling' => true))
            ->add('longitude', 'text', array('error_bubbling' => true));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['location_map'] = $this->map;
        $view->vars['child_class'] = 'six columns';
        $this->map->setHtmlContainerId($view->vars['id'].'_map');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\MapLocation',
            'compound' => true,
            'class' => 'error',
            'required' => false,
            'error_bubbling' => false,
        ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'map_location';
    }
}
