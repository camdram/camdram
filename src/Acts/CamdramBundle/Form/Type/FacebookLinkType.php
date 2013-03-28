<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Yaml\Yaml;

use Acts\CamdramBundle\Form\DataTransformer\FacebookLinkTransformer;

class FacebookLinkType extends AbstractType
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new FacebookLinkTransformer($this->api));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'medium',
            'service' => 'facebook',
            'label' => 'Facebook event/page',
            'attr' => array('placeholder' => 'URL, username or ID')
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'facebook_link';
    }
}
