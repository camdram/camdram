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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $choices = array(
            'Website' => 'website',
            'Server-based app' => 'server',
            'Other' => 'other',
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
