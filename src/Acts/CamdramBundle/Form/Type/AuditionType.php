<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class AuditionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_at', DateTimeType::class, ['date_widget' => 'single_text', 'time_widget' => 'single_text'])
            ->add('end_at', TimeType::class, ['widget' => 'single_text'])
            ->add('location')
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
                //endAt is only a Time field so ensure its date is correct
                $audition = $event->getData();
                $startAt = $audition->getStartAt();
                $endAt = $audition->getEndAt();

                $endAt->setDate($startAt->format('Y'), $startAt->format('m'), $startAt->format('d'));
                if ($endAt < $startAt) {
                    $endAt->modify('+1 day');
                }
            })
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Audition'
        ));
    }
}
