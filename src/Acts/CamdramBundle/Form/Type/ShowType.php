<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name')
            ->add('author', null, array('required' => false))
            ->add('description')
            ->add('image', 'image_upload', array('label' => 'Publicity image'))
            ->add('prices', null, array('required' => false, 'label' => 'Ticket prices'))
            ->add('venue')
            ->add('venue_name')
            ->add('society')
            ->add('society_name')
            ->add('booking_code', null, array('required' => false, 'label' => 'URL for buying tickets'))
            ->add('facebook_id', null, array('required' => false))
            ->add('twitter_id', null, array('required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Show'
        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_showtype';
    }
}
