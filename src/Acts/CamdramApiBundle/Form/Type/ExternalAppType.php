<?php

namespace Acts\CamdramApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ExternalAppType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('app_type', 'app_type')
            ->add('website')
            ->add('redirect_uris_string', 'textarea', array('label' => 'Redirect URIs', 'required' => false))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramApiBundle\Entity\ExternalApp'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acts_camdramapibundle_externalapp';
    }
}
