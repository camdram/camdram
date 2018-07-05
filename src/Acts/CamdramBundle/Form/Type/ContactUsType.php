<?php

namespace Acts\CamdramBundle\Form\Type;

use Acts\CamdramSecurityBundle\Security\User\CamdramUserInterface;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

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
            ->add('subject', 'text')
            ->add('message', 'textarea')
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            if ($this->storage && $this->storage->getToken()
                        && $this->storage->getToken()->getUser() instanceof CamdramUserInterface) {
                $user = $this->storage->getToken()->getUser();

                $form->add('name', HiddenType::class, ['data' => $user->getName(), 'read_only' => true])
                    ->add('email', HiddenType::class, ['data' => $user->getFullEmail(), 'read_only' => true]);
            } else {
                $form->add('name', 'text', [
                          'label' => 'Your name',
                          'constraints' => [
                                new NotBlank(),
                           ],
                        ])
                    ->add('email', 'email', [
                          'label' => 'Your email address',
                          'constraints' => [
                                new NotBlank(),
                                new Email(['checkMX' => true])
                            ],
                        ])
                    ->add('captcha', 'ewz_recaptcha', [
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
