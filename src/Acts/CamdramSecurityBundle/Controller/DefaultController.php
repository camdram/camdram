<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Event\UserEvent;
use Acts\CamdramSecurityBundle\Form\Type\CreatePasswordType;
use Acts\CamdramSecurityBundle\Form\Type\ForgottenPasswordType;
use Acts\CamdramSecurityBundle\Form\Type\ResetPasswordType;
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
        return $this->render('ActsCamdramSecurityBundle:Default:toolbar.html.twig');
    }
    
    public function loginAction(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            return $this->redirect($this->generateUrl('acts_camdram_homepage'));
        }
        
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError(false);
        if ($error instanceof AccountNotLinkedException)
        {
            return $this->forward('HWIOAuthBundle:Connect:connect');
        }
        
        return $this->render('ActsCamdramSecurityBundle:Default:login.html.twig', ['error' => $error]);
    }

    public function passwordLoginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $last_email = $authenticationUtils->getLastUsername();
        $has_error = !is_null($authenticationUtils->getLastAuthenticationError());
        
        if (!$last_email && $this->getUser() instanceof User)
        {
            $last_email = $this->getUser()->getEmail();
        }
        
        return $this->render(
            'ActsCamdramSecurityBundle:Default:login_form.html.twig',
            ['last_email' => $last_email, 'error' => $has_error]
        );
    }

    public function createAccountAction(Request $request)
    {
        if ($this->getUser()) {
            return $this->redirect($this->generateUrl('acts_camdram_homepage'));
        }
        $user = new User();

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

    public function confirmEmailAction($email, $token)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($email);
        if ($user && !$user->getIsEmailVerified()) {
            $expected_token = $this->get('camdram.security.token_generator')->generateEmailConfirmationToken($user);
            if ($token == $expected_token) {
                $user->setIsEmailVerified(true);
                $this->getDoctrine()->getManager()->flush();

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
                $user = $this->getDoctrine()->getManager()->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($data['email']);
                if ($user) {
                    $token = $this->get('camdram.security.token_generator')->generatePasswordResetToken($user);
                    $this->get('camdram.security.email_dispatcher')->sendPasswordResetEmail($user, $token);
                }

                //Always return the same response whether or not we find a user match
                return $this->render('ActsCamdramSecurityBundle:Default:forgotten_password_complete.html.twig', array(
                    'email' => $data['email']
                ));
            }
        }

        return $this->render('ActsCamdramSecurityBundle:Default:forgotten_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function resetPasswordAction($email, $token)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($email);
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
