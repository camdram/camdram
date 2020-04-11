<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Service\TokenGenerator;
use Acts\CamdramSecurityBundle\Service\EmailDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Acts\CamdramSecurityBundle\Form\Type\RegistrationType;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Handler\AuthenticationSuccessHandler;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;

class DefaultController extends AbstractController
{
    public function toolbarAction()
    {
        return $this->render('account/toolbar.html.twig');
    }

    public function confirmEmailAction($email, $token, TokenGenerator $tokenGenerator)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($email);
        if ($user && !$user->getIsEmailVerified()) {
            $expected_token = $tokenGenerator->generateEmailConfirmationToken($user);
            if ($token == $expected_token) {
                $user->setIsEmailVerified(true);
                $this->getDoctrine()->getManager()->flush();

                return $this->render('account/confirm_email.html.twig');
            }
        }

        return $this->render('account/confirm_email_error.html.twig');
    }

}
