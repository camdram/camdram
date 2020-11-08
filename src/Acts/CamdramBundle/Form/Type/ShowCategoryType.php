<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class ShowCategoryType
 *
 * A form type for show genres, The list of possible genres is pulled from a Yaml file.
 */
class ShowCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
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
        return ChoiceType::class;
    }
}
