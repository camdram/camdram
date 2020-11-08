<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class CollegeType
 *
 * A Form type represnting a Cambridge college. Is represented in a form as a drop-down box with a list of colleges,
 * the names of which are pulled from a Yaml file.
 */
class CollegeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
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
        return ChoiceType::class;
    }
}
