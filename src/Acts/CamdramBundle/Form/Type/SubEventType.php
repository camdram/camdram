<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', HiddenType::class, ['data' => '.'])
            ->add('description', HiddenType::class, ['data' => '.'])
            ->add('start_at', DateTimeType::class, [
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                    'model_timezone' => 'UTC',
                    'view_timezone' => 'Europe/London',
            ])
            ->add('endtime', TimeType::class, [
                    'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Event'
        ));
    }
}
