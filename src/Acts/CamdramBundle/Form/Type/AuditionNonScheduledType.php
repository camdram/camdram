<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Acts\CamdramBundle\Form\Type\FutureDateType;

class AuditionNonScheduledType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', new FutureDateType(), array('label' => 'Advert expiry date', 'widget' => 'single_text'))
            ->add('start_time', 'time', array('label' => 'Advert expiry time', 'widget' => 'single_text'))
            ->add('location', 'text', array('label' => 'Contact details'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Audition'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acts_camdrambundle_audition_non_scheduled';
    }
}
