<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class EmailBuilderType
 *
 * The form that's presented when a user adds/edits a email builder
 *
 * @package Acts\CamdramBundle\Form\Type
 */
class EmailBuilderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name')
            ->add('ToAddress')
            ->add('FromAddress')
            ->add('Subject')
            ->add('Introduction')
            ->add('IncludeTechieAdverts', 'checkbox', array('required' => false))
            ->add('IncludeAuditions', 'checkbox', array('required' => false))
            ->add('IncludeApplications', 'checkbox', array('required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\EmailBuilder'
        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_emailbuildertype';
    }
}
