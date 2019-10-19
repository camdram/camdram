<?php

namespace Acts\CamdramApiBundle\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Acts\CamdramApiBundle\Entity\ExternalApp;

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
            ->add('app_type', AppType::class)
            ->add('website', UrlType::class)
            ->add('redirect_uris_string', TextareaType::class, array('label' => 'Redirect URIs', 'required' => false))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $id = $event->getData() ? $event->getData()->getId() : null;
                $form = $event->getForm();
                //Only add captcha when creating a new ExternalApp
                if (!$id) {
                    $form->add('captcha', EWZRecaptchaType::class, [
                        'attr' => [
                            'options' => [
                                'theme' => 'clean'
                            ]
                        ],
                        'mapped' => false,
                        'constraints' => [
                            new RecaptchaTrue()
                        ]
                    ]);
                }
            });
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ExternalApp::class
        ));
    }
}
