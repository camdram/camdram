<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('start_at', DateTimeType::class, [
                'date_widget' => 'single_text', 'time_widget' => 'single_text',
                'model_timezone' => 'UTC',
                'view_timezone' => 'Europe/London',
                'constraints' => new Constraints\NotBlank()
            ])
            ->add('end_at', TimeType::class, [
                'widget' => 'single_text',
                'model_timezone' => 'UTC',
                'view_timezone' => 'Europe/London',
                'constraints' => new Constraints\NotBlank()
            ])
            ->add('location', TextType::class, ['constraints' => new Constraints\NotBlank()])
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
                //endAt is only a Time field so ensure its date is correct, taking timezones into account...
                $audition = $event->getData();
                $startAt = $audition->getStartAt();
                $endAtTime = $audition->getEndAt();

                // Symfony will handle this fine if we don't throw any exceptions (#604)
                if ($startAt == null || $endAtTime == null) return;

                $audition->setEndAt($this->generateEndAt($startAt, $endAtTime));
            })
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Audition',
            'constraints' => [
                new Constraints\Callback(function($audition, $context) {
                    if ($this->generateEndAt($audition->getStartAt(), $audition->getEndAt()) < new \DateTime()) {
                        $context->buildViolation('The end of the audition slot must be in the future.')
                                ->atPath('end_at')->addViolation();
                    }
                })
            ]
        ));
    }

    private function generateEndAt($startAt, $endAtTime): \DateTime
    {
        $endAtTime = clone $endAtTime;

        // Reverse model transform -> UTC to retrieve original time
        $endAtTime->setTimezone(new \DateTimezone('Europe/London'));

        $endAt = clone $startAt;
        // Reverse model transform -> UTC before setting date
        $endAt->setTimezone(new \DateTimezone('Europe/London'));
        $endAt->setTime($endAtTime->format('H'), $endAtTime->format('i'), $endAtTime->format('s'));
        // Convert back to UTC for serialization
        $endAt->setTimezone(new \DateTimezone('UTC'));

        // End time after start time then assume it's the next day
        if ($endAt < $startAt) $endAt->modify('+1 day');

        return $endAt;
    }
}
