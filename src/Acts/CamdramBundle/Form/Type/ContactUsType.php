<?php

namespace Acts\CamdramBundle\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Acts\CamdramSecurityBundle\Entity\User;

class ContactUsType extends AbstractType
{
    private $storage;

    public function __construct(TokenStorageInterface $storage)
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
            ->add('name', TextType::class, ['constraints' => [new NotBlank()]])
            ->add('email', EmailType::class, ['constraints' => [new NotBlank(), new Email(['checkMX' => true])]])
            ->add('subject', TextType::class)
            ->add('message', TextareaType::class)
            ->add('captcha', 'ewz_recaptcha', [
                'attr' => [
                    'options' => [
                        'theme' => 'clean'
                    ]
                ],
                'constraints' => [
                    new IsTrue()
                ]
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) 
        {    
            $user = $this->storage->getToken() ? $this->storage->getToken()->getUser() : null;

            if ($user instanceof User) {

                $event->setData(['name' => $user->getName(), 'email' => $user->getEmail()]);
            }
        });
    }
}
