<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Form\Type\CreatePasswordType;
use Acts\CamdramSecurityBundle\Form\Type\ForgottenPasswordType;
use Acts\CamdramSecurityBundle\Form\Type\ResetPasswordType;
use Acts\CamdramSecurityBundle\Service\TokenGenerator;
use Acts\CamdramSecurityBundle\Service\EmailDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Acts\CamdramSecurityBundle\Form\Type\RegistrationType;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Handler\AuthenticationSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;

class DefaultController extends Controller
{
    public function toolbarAction()
    {
        return $this->render('account/toolbar.html.twig');
    }
    
    public function loginAction(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('acts_camdram_homepage'));
        }
        
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError(false);
        if ($error instanceof AccountNotLinkedException) {
            return $this->forward('HWIOAuthBundle:Connect:connect');
        }
        
        return $this->render('account/login.html.twig', ['error' => $error]);
    }

    public function passwordLoginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $last_email = $authenticationUtils->getLastUsername();
        $has_error = !is_null($authenticationUtils->getLastAuthenticationError());
        
        if (!$last_email && $this->getUser() instanceof User) {
            $last_email = $this->getUser()->getEmail();
        }
        
        return $this->render(
            'account/login_form.html.twig',
            ['last_email' => $last_email, 'error' => $has_error]
        );
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

    public function forgottenPasswordAction(Request $request, TokenGenerator $tokenGenerator, EmailDispatcher $emailDispatcher)
    {
        $email = $this->getUser() ? $this->getUser()->getEmail() : null;
        $form = $this->createForm(ForgottenPasswordType::class, ['email' => $email]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $user = $this->getDoctrine()->getManager()->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($data['email']);
                if ($user) {
                    $token = $tokenGenerator->generatePasswordResetToken($user);
                    $emailDispatcher->sendPasswordResetEmail($user, $token);
                }

                //Always return the same response whether or not we find a user match
                return $this->render('account/forgotten_password_complete.html.twig', array(
                    'email' => $data['email']
                ));
            }
        }

        return $this->render('account/forgotten_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function resetPasswordAction($email, $token, Request $request, TokenGenerator $tokenGenerator)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($email);
        if ($user) {
            $expected_token = $tokenGenerator->generatePasswordResetToken($user);
            if ($token == $expected_token) {
                $form = $this->createForm(ResetPasswordType::class, array());
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        $data = $form->getData();
                        $factory = $this->get('security.encoder_factory');
                        $encoder = $factory->getEncoder($user);
                        $password = $encoder->encodePassword($data['password'], $user->getSalt());
                        $user->setPassword($password);
                        $this->getDoctrine()->getManager()->flush();

                        return $this->render('account/reset_password_complete.html.twig', array(
                            'email'     => $email,
                        ));
                    }
                }

                return $this->render('account/reset_password.html.twig', array(
                    'email' => $email,
                    'token' => $token,
                    'form' => $form->createView()
                ));
            }
        }

        return $this->render('account/reset_password_error.html.twig', array());
    }
}
