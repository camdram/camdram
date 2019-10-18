<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemeColorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['theme_color_message' => '']);
    }

    public function buildView($view, $form, array $options)
    {
        $view->vars['theme_color_message'] = $options['theme_color_message'];
    }

    public function getParent()
    {
        return TextType::class;
    }
}
