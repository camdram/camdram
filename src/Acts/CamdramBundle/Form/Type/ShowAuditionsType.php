<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * Class ShowType
 *
 * The form that's presented when a user adds/edits a show
 */
class ShowAuditionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('aud_extra', TextareaType::class, array('required' => false, 'label' => 'Information to display on auditions page'))
            ->add('scheduled_auditions', CollectionType::class, array(
                'entry_type' => AuditionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Sessions'
            ))
            ->add('non_scheduled_auditions', CollectionType::class, array(
                'entry_type' => AuditionNonScheduledType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Contact Details'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Show',
            'cascade_validation' => true,
        ));
    }
}
