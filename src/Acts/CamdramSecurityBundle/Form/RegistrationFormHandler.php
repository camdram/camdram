<?php
namespace Acts\CamdramSecurityBundle\Form;

use HWI\Bundle\OAuthBundle\Form\RegistrationFormHandlerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Acts\CamdramSecurityBundle\Form\Type\ExternalRegistrationType;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Event\UserEvent;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RegistrationFormHandler implements RegistrationFormHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var EncoderFactoryInterface */
    private $encoderFactory;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        EncoderFactoryInterface $encoderFactory,
        FormFactoryInterface $formFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $entityManager;
        $this->encoderFactory = $encoderFactory;
        $this->formFactory = $formFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /** @return \Symfony\Component\Form\FormInterface */
    public function createForm()
    {
        return $this->formFactory->create(ExternalRegistrationType::class, new User());
    }

    public function process(Request $request, Form $form, UserResponseInterface $userInformation)
    {
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /** @var \Acts\CamdramSecurityBundle\Entity\User $user */
                $user = $form->getData();
                $user->setIsEmailVerified($user->getEmail() == $userInformation->getEmail());
                $user->setProfilePictureUrl($userInformation->getProfilePicture());
                $this->em->persist($user);
                $this->em->flush();

                return true;
            }
        }
        return false;
    }
};
