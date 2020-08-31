<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('start_at', DateTimeType::class, [
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                    'model_timezone' => 'UTC',
                    'view_timezone' => 'Europe/London',
                ])
            ->add('endtime', TimeType::class, [
                    'widget' => 'single_text',
            ])
            ->add('linked_dates', CollectionType::class, [
                    'entry_type' => SubEventType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
            ])
            ->add('societies', CollectionType::class, [
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => 'Societies',
                    'required' => false,
                    'mapped' => false
            ])
            ->add('theme_color', ThemeColorType::class, ['theme_color_message' =>
                    'Setting a colour for your event makes it stand out around Camdram.',
                    'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Event'
        ));
    }
}
