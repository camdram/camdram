<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Acts\CamdramBundle\Entity\Performance;

/**
 * Class ShowType
 *
 * The form that's presented when a user adds/edits a show
 */
class ShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('author', null, array('required' => false))
            ->add('description')
            ->add('content_warning')
            ->add('prices', null, array('required' => false, 'label' => 'Ticket prices', 'attr' => array(
                'placeholder' => 'e.g. £6/5'
            )))
            ->add('multi_venue', ChoiceType::class, array(
                'expanded' => true,
                'mapped' => false,
                'choices' => array(
                    'All the performances are at the same venue (e.g. an ADC mainshow/lateshow)' => 'single',
                    'The performances are at a number of different venues (e.g. a tour)' => 'multi',
                ),
            ))
            ->add('performances', CollectionType::class, array(
                'entry_type' => PerformanceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Dates and times',
                'prototype_data' => new Performance(),
            ))
            ->add('category', ShowCategoryType::class)
            ->add('venue', TextType::class, array(
                'required' => false,
                'mapped' => false
            ))
            ->add('online_booking_url', UrlType::class, array(
                'required' => false, 'label' => 'Ticket URL'
            ))
            ->add('facebook_id', FacebookLinkType::class, array('required' => false))
            ->add('twitter_id', TwitterLinkType::class, array('required' => false))
            ->add('societies', CollectionType::class, array(
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => 'Societies',
                    'required' => false,
                    'mapped' => false
            ))
            ->add('theme_color', ThemeColorType::class, ['theme_color_message' =>
                    'Setting a colour for your show makes it stand out around Camdram.',
                    'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Show'
        ));
    }
}
