<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Event\UserEvent;
use Acts\CamdramSecurityBundle\Form\Type\ForgottenPasswordType;
use Acts\CamdramSecurityBundle\Form\Type\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\SecurityContext;

use Acts\CamdramSecurityBundle\Form\Type\LoginType,
    Acts\CamdramSecurityBundle\Form\Type\RegistrationType,
    Acts\CamdramSecurityBundle\Entity\UserIdentity,
    Acts\CamdramBundle\Entity\User,
    Acts\CamdramSecurityBundle\Security\Handler\AuthenticationSuccessHandler;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class DefaultController extends Controller
{

    public function toolbarAction()
    {
        return $this->render('ActsCamdramSecurityBundle:Default:toolbar.html.twig', array(
            'services' => $this->get('external_login.service_provider')->getServices()
        ));
    }

    public function loginAction()
    {
        $session = $this->getRequest()->getSession();

        $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        $session->remove(SecurityContext::AUTHENTICATION_ERROR);

        if ($session->get(SecurityContext::LAST_USERNAME)) {
            $last_email = $session->get(SecurityContext::LAST_USERNAME);
        }
        elseif ($this->getUser() instanceof User) {
            $last_email = $this->getUser()->getEmail();
        }
        elseif ($this->getUser() instanceof ExternalUser && $this->getUser()->getUser()) {
            $last_email = $this->getUser()->getUser()->getEmail();
        }
        else {
            $last_email = '';
        }

        if ($session->get('_security.last_exception') instanceof InsufficientAuthenticationException) {
            return $this->render(
                'ActsCamdramSecurityBundle:Default:relogin.html.twig',
                array(
                    'last_email'    => $last_email,
                    'error'         => $error,
                )
            );
        }
        else {
            return $this->render(
                'ActsCamdramSecurityBundle:Default:login.html.twig',
                array(
                    'services'      => $this->get('external_login.service_provider')->getServices(),
                    'last_email'    => $last_email,
                    'error'         => $error,
                )
            );
        }

    }

    public function createAccountAction(Request $request)
    {
        $user = new User;

        $form = $this->createForm(new RegistrationType(), $user);

        if ($request->getMethod() == 'POST') {
            $form->submit($request);
            if ($form->isValid()) {
                $user = $form->getData();

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $token = new UsernamePasswordToken($user, $user->getPassword(), 'public', $user->getRoles());
                $this->get('event_dispatcher')->dispatch(CamdramSecurityEvents::REGISTRATION_COMPLETE, new UserEvent($user));
                $this->get('security.context')->setToken($token);
                $this->get('camdram.security.authentication_success_handler')->onAuthenticationSuccess($request, $token);
                return $this->redirect($this->generateUrl('acts_camdram_security_create_account_complete'));
            }

        }
        return $this->render('ActsCamdramSecurityBundle:Default:create_account.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function createAccountCompleteAction()
    {
        return $this->render('ActsCamdramSecurityBundle:Default:create_account_complete.html.twig', array());
    }

    public function linkUserAction(Request $request)
    {
        $link_token = $this->getRequest()->getSession()->get(AuthenticationSuccessHandler::NEW_TOKEN);
        if (!$link_token) {
            return $this->redirect($this->generateUrl('acts_camdram_homepage'));
        }
        $user = $this->getUser();
        $link_user = $link_token->getUser();
        if ($link_user instanceof ExternalUser) {
            $link_user = $this->get('camdram.security.external_user.provider')->refreshUser($link_user);
        }
        elseif ($link_user instanceof User) {
            $link_user = $this->get('camdram.security.user.provider')->refreshUser($link_user);
        }

        if ($request->getMethod() == 'POST') {
            if ($request->request->has('link')) {
                $this->get('camdram.security.user_linker')->linkUsers($user, $link_user);
                $request->getSession()->remove(AuthenticationSuccessHandler::LAST_AUTHENTICATION_TOKEN);
                $token = $this->get('camdram.security.user_linker')->findCamdramToken($link_token, $this->get('security.context')->getToken());
                $this->get('security.context')->setToken($token);
                return $this->get('camdram.security.authentication_success_handler')->onAuthenticationSuccess($request, $token);
            }
            elseif ($request->request->has('old')) {
                return $this->get('camdram.security.authentication_success_handler')->onAuthenticationSuccess($request, $this->get('security.context')->getToken());
            }
            else {
                $request->getSession()->clear();
                $this->get('security.context')->setToken($link_token);
                return $this->get('camdram.security.authentication_success_handler')->onAuthenticationSuccess($request, $link_token);
            }
        }

        return $this->render('ActsCamdramSecurityBundle:Default:link_user.html.twig', array(
            'user' => $user,
            'link_user' => $link_user
        ));
    }

    public function confirmEmailAction($email, $token)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:User')->findOneByEmail($email);
        if ($user && !$user->getIsEmailVerified()) {
            $expected_token = $this->get('camdram.security.token_generator')->generateEmailConfirmationToken($user);
            if ($token == $expected_token) {
                $user->setIsEmailVerified(true);
                $this->getDoctrine()->getManager()->flush();
                $this->get('event_dispatcher')->dispatch(CamdramSecurityEvents::EMAIL_VERIFIED, new UserEvent($user));
                return $this->render('ActsCamdramSecurityBundle:Default:confirm_email.html.twig', array(
                    'confirm_user' => $user,
                    'services'      => $this->get('external_login.service_provider')->getServices(),
                ));
            }
        }
        return $this->render('ActsCamdramSecurityBundle:Default:confirm_email_error.html.twig', array());
    }

    public function forgottenPasswordAction()
    {
        $email = $this->getUser() ? $this->getUser()->getEmail() : null;
        $form = $this->createForm(new ForgottenPasswordType(), array('email' => $email));

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->submit($this->getRequest());
            if ($form->isValid()) {
                $data = $form->getData();
                $user = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:User')->findOneByEmail($data['email']);
                if ($user) {
                    $token = $this->get('camdram.security.token_generator')->generatePasswordResetToken($user);
                    $this->get('camdram.security.email_dispatcher')->sendPasswordResetEmail($user, $token);
                    return $this->render('ActsCamdramSecurityBundle:Default:forgotten_password_complete.html.twig', array(
                        'email' => $data['email']
                    ));
                }
                else {
                    $form->get('email')->addError(new FormError('A user cannot be found with that email address'));
                }
            }
        }
        return $this->render('ActsCamdramSecurityBundle:Default:forgotten_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function resetPasswordAction($email, $token)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:User')->findOneByEmail($email);
        if ($user) {
            $expected_token = $this->get('camdram.security.token_generator')->generatePasswordResetToken($user);
            if ($token == $expected_token) {

                $form = $this->createForm(new ResetPasswordType(), array());
                if ($this->getRequest()->getMethod() == 'POST') {
                    $form->submit($this->getRequest());
                    if ($form->isValid()) {
                        $data = $form->getData();
                        $factory = $this->get('security.encoder_factory');
                        $encoder = $factory->getEncoder($user);
                        $password = $encoder->encodePassword($data['password'], $user->getSalt());
                        $user->setPassword($password);
                        $this->getDoctrine()->getManager()->flush();
                        return $this->render('ActsCamdramSecurityBundle:Default:reset_password_complete.html.twig', array(
                            'email'     => $email,
                            'services'  => $this->get('external_login.service_provider')->getServices(),
                        ));
                    }
                }

                return $this->render('ActsCamdramSecurityBundle:Default:reset_password.html.twig', array(
                    'email' => $email,
                    'token' => $token,
                    'form' => $form->createView()
                ));
            }
        }
        return $this->render('ActsCamdramSecurityBundle:Default:reset_password_error.html.twig', array());
    }

}