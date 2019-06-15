<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SocietyType
 *
 * The form that's presented when a user adds/edits a society
 */
class SocietyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* Short names must be included to generate URL slugs. */
        $builder
            ->add('name')
            ->add('short_name', null, array('required' => true))
            ->add('description')
            ->add('college', CollegeType::class)
            ->add('facebook_id', FacebookLinkType::class)
            ->add('twitter_id', TwitterLinkType::class)
            ->add('theme_color', ThemeColorType::class, ['theme_color_message' =>
                    'Setting a colour changes the site theme when viewing this page.',
                    'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Society'
        ));
    }
}
