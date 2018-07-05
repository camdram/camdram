<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class ApplicationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextType::class, array('label' => 'Brief Description',
                'attr' => array('placeholder' => 'e.g. "Applications to Direct" (your show name will be included automatically)')))
            ->add('furtherInfo', TextareaType::class, array('label' => 'Further Information'))
            ->add('deadlineDate', DateType::class, array('widget' => 'single_text', 'error_bubbling' => false))
            ->add('deadlineTime', TimeType::class, array('widget' => 'single_text'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Application'
        ));
    }
}
