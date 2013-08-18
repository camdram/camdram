<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\SecurityContext;

use Acts\CamdramSecurityBundle\Form\Type\LoginType,
    Acts\CamdramSecurityBundle\Form\Type\RegistrationType,
    Acts\CamdramSecurityBundle\Entity\UserIdentity,
    Acts\CamdramSecurityBundle\Security\Authentication\Token\CamdramUserTokenService,
    Acts\CamdramBundle\Entity\User;

class DefaultController extends Controller
{

    public function redirectAction($service)
    {
        return new RedirectResponse($this->container->get('camdram.security.utils')->getAuthorizationUrl($service));
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
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'form'          => $form->createView(),
                'error'         => $error,
            )
        );
    }

    public function noExistingUserAction(Request $request)
    {
        $form = $this->createForm(new LoginType(), array('email' => $request->get('email')));

        $token = $this->get('security.context')->getToken();
        $service = $token->getLastService();

        if ($request->getMethod() == 'POST') {
            if ($request->request->has('new_user')) {
                return $this->redirect($this->generateUrl('camdram_security_new_user'));
            }

            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $user = $this->getDoctrine()->getRepository('ActsCamdramBundle:User')->findByEmailAndPassword($data['email'], $data['password']);
                if ($user) {
                    $this->addIdentity($user, $service);

                    $this->get('session')->set('new_local_user_id', $user->getId());
                    return $this->redirect($this->generateUrl('camdram_security_login', array('service' => 'local')));
                }
                else {
                    $form->addError(new \Symfony\Component\Form\FormError('An account cannot be found for that username and password'));
                }
            }
        }

        return $this->render('ActsCamdramSecurityBundle:Default:no_existing_user.html.twig', array(
            'form' => $form->createView(),
            'service' => $service,
        ));
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


    public function confirmAddIdentityAction(Request $request)
    {

        $token = $this->get('security.context')->getToken();
        $service = $token->getLastService();

        if ($request->getMethod() == 'POST') {
            if ($request->request->has('yes')) {
                $user = $this->get('security.context')->getToken()->getUser();
                $this->addIdentity($user, $service);
            }
            else {
                $token->removeLastService();
            }
            return $this->redirect($this->generateUrl('camdram_security_login', array('service' => 'complete')));
        }

        return $this->render('ActsCamdramSecurityBundle:Default:confirm_add_identity.html.twig', array(
            'service' => $service
        ));
    }

    private function addIdentity(User $user, CamdramUserTokenService $service)
    {
        if ($service->getName() == 'local') return;

        $i = new UserIdentity();
        $i->setService($service->getName());
        $i->setRemoteId($service->getUserInfo('id'));
        $i->setRemoteUser($service->getUserInfo('username'));
        $i->setUser($user);
        $user->addIdentity($i);

        $em = $this->getDoctrine()->getManager();
        $em->persist($i);
        $em->flush();
    }

}
