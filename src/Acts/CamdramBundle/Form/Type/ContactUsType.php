<?php

namespace Acts\CamdramBundle\Form\Type;

use Acts\CamdramSecurityBundle\Security\User\CamdramUserInterface;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class ContactUsType extends AbstractType
{
    private $storage;

    public function __construct(TokenStorageInterface $storage = null)
    {
        $this->storage = $storage;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class)
            ->add('message', TextareaType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            if ($this->storage && $this->storage->getToken()
                        && $this->storage->getToken()->getUser() instanceof CamdramUserInterface) {
                $user = $this->storage->getToken()->getUser();

                $form->add('name', 'hidden', ['data' => $user->getName(), 'read_only' => true])
                    ->add('email', 'hidden', ['data' => $user->getFullEmail(), 'read_only' => true]);
            } else {
                $form->add('name', TextType::class, [
                          'label' => 'Your name',
                          'constraints' => [
                                new NotBlank(),
                           ],
                        ])
                    ->add('email', EmailType::class, [
                          'label' => 'Your email address',
                          'constraints' => [
                                new NotBlank(),
                                new Email(['checkMX' => true])
                            ],
                        ])
                    ->add('captcha', EWZRecaptchaType::class, [
                        'attr' => [
                            'options' => [
                                'theme' => 'clean'
                            ]
                        ],
                        'mapped'      => false,
                        'constraints' => [
                            new IsTrue()
                        ]
                    ]);
            }
        });
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acts_camdrambundle_contact_us';
    }
}
