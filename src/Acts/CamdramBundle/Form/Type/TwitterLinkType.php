<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Yaml\Yaml;

use Acts\CamdramBundle\Form\DataTransformer\TwitterLinkTransformer;

class TwitterLinkType extends AbstractType
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new TwitterLinkTransformer($this->api));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'medium',
            'service' => 'twitter',
            'label' => 'Twitter account name or URL',
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'twitter_link';
    }
}
