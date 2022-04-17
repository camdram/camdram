<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Acts\CamdramBundle\Form\DataTransformer\FacebookLinkTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class FacebookLinkType
 *
 * Form type for Facebook event/page. The user just sees an input box, but some clever stuff on the backend uses
 * the Facebook API to do some validating and convert it into a Facebook ID.
 */
class FacebookLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new FacebookLinkTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => 'medium',
            'service' => 'facebook',
            'label' => 'Facebook event/page',
            'attr' => ['placeholder' => 'Facebook URL'],
            'required' => false,
            'invalid_message' => "This should be an URL starting “https://www.facebook.com/”; other URLs can go in the desciption.",
        ]);
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }
}
