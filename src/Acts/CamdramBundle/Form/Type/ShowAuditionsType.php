<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Acts\CamdramBundle\Form\DataTransformer\PerformanceExcludeTransformer;

/**
 * Class ShowType
 *
 * The form that's presented when a user adds/edits a show
 *
 * @package Acts\CamdramBundle\Form\Type
 */
class ShowAuditionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('aud_extra', 'textarea', array('required' => false, 'label' => 'Information to display on auditions page'))
            ->add('scheduled_auditions', 'collection', array(
                'type' => new AuditionType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Sessions'
            ))
            ->add('non_scheduled_auditions', 'collection', array(
                'type' => new AuditionNonScheduledType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Contact Details'
            ))
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
        return 'acts_camdrambundle_showtype_auditions';
    }
}
