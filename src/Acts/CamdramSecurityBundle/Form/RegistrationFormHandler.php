<?php
namespace Acts\CamdramSecurityBundle\Form;

use HWI\Bundle\OAuthBundle\Form\RegistrationFormHandlerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Acts\CamdramSecurityBundle\Form\Type\ExternalRegistrationType;
use Acts\CamdramSecurityBundle\Entity\User;
use Symfony\Component\Form\FormFactoryInterface;

class RegistrationFormHandler implements RegistrationFormHandlerInterface
{
    private $em;
    
    private $encoderFactory;
    
    private $formFactory;
    
    public function __construct(
    
        EntityManager $entityManager,
    
        EncoderFactoryInterface $encoderFactory,
        FormFactoryInterface $formFactory
    
    ) {
        $this->em = $entityManager;
        $this->encoderFactory = $encoderFactory;
        $this->formFactory = $formFactory;
    }
    
    public function createForm()
    {
        return $this->formFactory->create(new ExternalRegistrationType(), new User());
    }
    
    public function process(Request $request, Form $form, UserResponseInterface $userInformation)
    {
        if ($request->getMethod() == 'POST') {
            $form->submit($request);
            if ($form->isValid()) {
                /** @var \Acts\CamdramSecurityBundle\Entity\User $user */
                $user = $form->getData();
                $user->setEmail($userInformation->getEmail());
                $user->setIsEmailVerified(true);
                $user->setProfilePictureUrl($userInformation->getProfilePicture());
                $this->em->persist($user);
                $this->em->flush();
                
                return true;
            }
        }
        return false;
    }
};
