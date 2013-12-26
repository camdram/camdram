<?php

namespace Acts\CamdramApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Yaml\Yaml;

/**
 * Class AppType
 *
 * @package Acts\CamdramBundle\Form\Type
 */

class AppType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = array(
            'website' => 'Website',
            'server' => 'Server-based app',
            'other' => 'Other'
        );

        $resolver->setDefaults(array(
                'choices' => $choices,
                'label' => 'App Type',
                'expanded' => true,
                'required' => true,
            ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'app_type';
    }
}
