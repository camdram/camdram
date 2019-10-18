<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AuditionNonScheduledType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_at', DateTimeType::class, ['date_widget' => 'single_text',
                 'time_widget' => 'single_text',
                 'constraints' => [new Constraints\NotBlank(), new Constraints\GreaterThanOrEqual("now")]])
            ->add('location', TextType::class, [
                 'label' => 'Contact details',
                 'constraints' => [new Constraints\NotBlank()]])
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
