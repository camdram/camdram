<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Base\Coordinate;

/**
 * Class MapLocationType
 *
 * A form type representing a location on a map, or a longitude/latitude pair. It is rendered as a click-able
 * Google Map (with the help of some Javascript), which gracefully degrades to two input boxes.
 */
class MapLocationType extends AbstractType
{
    private $map;

    public function __construct(array $mapCenter)
    {
        $map = new Map;
        $map->setCenter(new Coordinate($mapCenter[0], $mapCenter[1]));
        $map->setMapOption('zoom', 14);
        $map->setStylesheetOptions(array('width' => '100%', 'height' => '100%'));

        $this->map = $map;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('latitude', TextType::class, array('error_bubbling' => true))
            ->add('longitude', TextType::class, array('error_bubbling' => true));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['location_map'] = $this->map;
        $view->vars['child_class'] = 'six columns';
        $this->map->setHtmlId($view->vars['id'].'_map');
    }

    public function configureOptions(OptionsResolver $resolver)
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
