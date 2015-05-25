<?php

namespace Acts\CamdramBundle\Form\Type;

use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\User\CamdramUserInterface;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\True;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ContactUsType extends AbstractType
{
    private $storage;

    public function __construct(TokenStorageInterface $storage = null)
    {
        $this->storage = $storage;
    }

        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', 'text')
            ->add('message', 'textarea')
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();

            if ($this->storage && $this->storage->getToken()
                        && $this->storage->getToken()->getUser() instanceof CamdramUserInterface) {
                $user = $this->storage->getToken()->getUser();

                $form->add('name', 'hidden', array('data' => $user->getName(), 'read_only' => true))
                    ->add('email', 'hidden', array('data' => $user->getFullEmail(), 'read_only' => true));
            }
            else {
                $form->add('name', 'text', array('label' => 'Your name'))
                    ->add('email', 'email', array('label' => 'Your email address'))
                    ->add('captcha', 'ewz_recaptcha', array(
                        'attr' => array(
                            'options' => array(
                                'theme' => 'clean'
                            )
                        ),
                        'mapped'      => false,
                        'constraints' => array(
                            new True()
                        )
                    ));
            }

        });
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acts_camdrambundle_contact_us';
    }
}
