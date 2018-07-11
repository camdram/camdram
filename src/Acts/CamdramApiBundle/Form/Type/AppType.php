<?php

namespace Acts\CamdramApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class AppType
 */
class AppType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
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
        return ChoiceType::class;
    }
}
