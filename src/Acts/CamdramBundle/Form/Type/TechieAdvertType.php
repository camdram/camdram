<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class TechieAdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('positions', TextareaType::class, array('label' => 'Vacant positions (one per line)'))
            ->add('contact', TextType::class, array('label' => 'Contact details'))
            ->add('deadline', CheckboxType::class, array('label' => 'Include a deadline for applications', 'required' => false))
            ->add('expiry', DateTimeType::class, [
                    'label' => 'Deadline date and time',
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                    'model_timezone' => 'UTC',
                    'view_timezone' => 'Europe/London',
                ])
            ->add('tech_extra', TextareaType::class, array('required' => false,
                'label' => 'Further information that might be useful to people applying for this/these positions'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\TechieAdvert'
        ));
    }
}
