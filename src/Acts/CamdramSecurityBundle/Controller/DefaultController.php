<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\SecurityContext;

use Acts\CamdramSecurityBundle\Form\Type\LoginType,
    Acts\CamdramSecurityBundle\Form\Type\RegistrationType,
    Acts\CamdramSecurityBundle\Entity\UserIdentity,
    Acts\CamdramBundle\Entity\User,
    Acts\CamdramSecurityBundle\Security\Handler\AuthenticationSuccessHandler;

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
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $formBuilder = $this->createFormBuilder(array('email' => $request->get('email'), 'remember_me' => null))
            ->add('email')
            ->add('password', 'password');

        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $formBuilder->add('remember_me', 'checkbox', array('label' => 'Automatically log in on this computer next time', 'required' => false));
        }
        $form = $formBuilder->getForm();

        return $this->render(
            'ActsCamdramSecurityBundle:Default:login.html.twig',
            array(
                // last username entered by the user
                'services' => $this->get('external_login.service_provider')->getServices(),
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'form'          => $form->createView(),
                'error'         => $error,
            )
        );
    }

    public function newUserAction(Request $request)
    {
        $token = $this->get('security.context')->getToken();
        $service = $token->getLastService();
        $complete = $service->getUserInfo('email') && $service->getUserInfo('name');

        $user = new User;
        $user->setName($service->getUserInfo('name'));
        $user->setEmail($service->getUserInfo('email'));

        $errors = $this->get('validator')->validate($user);

        if (count($errors) == 0) {
            //We've been passed enough information to create the account straight away
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addIdentity($user, $service);

            $token->setUser($user);
            $token->setAuthenticated(true);

            return $this->redirect($this->generateUrl('camdram_security_login', array('service' => 'complete')));
        }
        else {
            $form = $this->createForm(new RegistrationType(), $user);

            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $user = $form->getData();
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $this->addIdentity($user, $service);

                    $token->setUser($user);
                    $token->setAuthenticated(true);

                    return $this->redirect($this->generateUrl('camdram_security_login', array('service' => 'complete')));
                }

            }
            return $this->render('ActsCamdramSecurityBundle:Default:new_user.html.twig', array(
                'form' => $form->createView(),
            ));
        }

    }

    public function linkUserAction(Request $request)
    {
        $link_token = $this->getRequest()->getSession()->get(AuthenticationSuccessHandler::NEW_TOKEN);
        if (!$link_token) {
            return $this->redirect($this->generateUrl('acts_camdram_homepage'));
        }
        $user = $this->getUser();
        $link_user = $link_token->getUser();

        if ($request->getMethod() == 'POST') {
            if ($request->request->has('link')) {
                $this->get('camdram.security.user_linker')->linkUsers($user, $link_user);
                $request->getSession()->remove(AuthenticationSuccessHandler::LAST_AUTHENTICATION_TOKEN);
                $token = $this->get('camdram.security.user_linker')->findCamdramToken($link_token, $this->get('security.context')->getToken());
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

}
