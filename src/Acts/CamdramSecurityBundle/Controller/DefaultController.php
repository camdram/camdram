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
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function toolbarAction()
    {
        return $this->render('account/toolbar.html.twig');
    }

    public function confirmEmailAction($email, $token, TokenGenerator $tokenGenerator)
    {
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneByEmail($email);
        if ($user && !$user->getIsEmailVerified()) {
            if ($tokenGenerator->verifyEmailConfirmationToken($user, $token)) {
                $user->setIsEmailVerified(true);
                $this->getDoctrine()->getManager()->flush();

                return $this->render('account/confirm_email.html.twig');
            }
        }

        return $this->render('account/confirm_email_error.html.twig');
    }

    /**
     * @Route("/test-login-handler", name="auth_test_login")
     */
    public function testLoginAction(Request $request)
    {
        if ($this->getParameter('kernel.environment') !== 'test') {
            throw $this->createNotFoundException('Test login is only valid in test environment');
        }

        if ($request->getMethod() == 'POST') {
            $data = [
                'identifier' => $request->request->get('identifier'),
                'name' => $request->request->get('name'),
                'email' => $request->request->get('email'),
            ];
            $redirect_uri = $request->request->get('redirect_uri');
            $token = base64_encode(json_encode($data));
            return $this->redirect($redirect_uri.'?test-token='.$token);
        } else {
            return $this->render('account/test_login.html.twig', [
                'redirect_uri' => $request->query->get('redirect_uri'),
            ]);
        }
    }

}
