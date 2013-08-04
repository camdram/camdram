<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Yaml\Yaml;

use Acts\CamdramBundle\Form\DataTransformer\TwitterLinkTransformer;

/**
 * Class TwitterLinkType
 *
 * Form type for a Twitter account. The user just sees an input box, but some clever stuff on the backend uses
 * the Twitter API to do some validating and convert it into a Twitter account ID.

 * @package Acts\CamdramBundle\Form\Type
 */
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
            'label' => 'Twitter account',
            'attr' => array('placeholder' => 'URL or username')
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
