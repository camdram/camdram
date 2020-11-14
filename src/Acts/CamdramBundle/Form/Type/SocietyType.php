<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SocietyType
 *
 * The form that's presented when a user adds/edits a society
 */
class SocietyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /* Short names must be included to generate URL slugs. */
        $builder
            ->add('name')
            ->add('short_name', null, array('required' => true))
            ->add('description')
            ->add('college', CollegeType::class)
            ->add('facebook_id', FacebookLinkType::class)
            ->add('twitter_id', TwitterLinkType::class)
            ->add('theme_color', ThemeColorType::class, ['theme_color_message' =>
                    'Setting a colour changes the site theme when viewing this page.',
                    'required' => false])
            ->add('contact_email', EmailType::class, ['help' =>
                    'This is the email the contact form goes to and is not publicly displayed. '.
                    'Setting this to an email address that will be valid in the long term rather than '.
                    'an individual\'s personal email is recommended. By default the contact form '.
                    'goes to all admins for this society.',
                    'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Society'
        ));
    }
}
