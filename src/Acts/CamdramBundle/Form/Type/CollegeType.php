<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Yaml\Yaml;

/**
 * Class CollegeType
 *
 * A Form type represnting a Cambridge college. Is represented in a form as a drop-down box with a list of colleges,
 * the names of which are pulled from a Yaml file.
 *
 * @package Acts\CamdramBundle\Form\Type
 */

class CollegeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $file = __DIR__.'/../../Resources/config/colleges.yml';
        $data = Yaml::parse(file_get_contents($file));
        $choices = array();

        foreach ($data as $val) {
            $choices[$val] = $val;
        }

        $resolver->setDefaults(array(
            'choices' => $choices,
            'empty_value' => '',
            'empty_data' => null,
            'label' => 'College (if applicable)',
            'required' => false,
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'college';
    }
}
