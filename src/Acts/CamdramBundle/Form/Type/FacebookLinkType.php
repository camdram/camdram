<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Acts\CamdramBundle\Form\DataTransformer\FacebookLinkTransformer;

/**
 * Class FacebookLinkType
 *
 * Form type for Facebook event/page. The user just sees an input box, but some clever stuff on the backend uses
 * the Facebook API to do some validating and convert it into a Facebook ID.
 */
class FacebookLinkType extends AbstractType
{
    /**
     * 
     * @var \Facebook\Facebook
     */
    private $api;

    public function __construct(\Facebook\Facebook $api)
    {
        $this->api = $api;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new FacebookLinkTransformer($this->api));
    }

    public function configureOptions(OptionsResolver $resolver)
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
