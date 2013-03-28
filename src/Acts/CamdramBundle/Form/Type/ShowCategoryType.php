<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Yaml\Yaml;

class ShowCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $file = __DIR__.'/../../Resources/config/categories.yml';
        $data = Yaml::parse(file_get_contents($file));
        $choices = array();

        foreach ($data as $val) {
            $choices[$val] = $val;
        }

        $resolver->setDefaults(array(
            'choices' => $choices,
            'empty_value' => '',
            'empty_data' => null,
            'label' => 'Category',
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'show_category';
    }
}
