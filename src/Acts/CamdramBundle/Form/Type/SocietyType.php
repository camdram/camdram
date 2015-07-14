<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SocietyType
 *
 * The form that's presented when a user adds/edits a society
 */
class SocietyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('short_name')
            ->add('description')
            ->add('image', 'image_upload', array('label' => 'Logo'))
            ->add('college', 'college')
            ->add('facebook_id')
            ->add('twitter_id')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Society'
        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_societytype';
    }
}
