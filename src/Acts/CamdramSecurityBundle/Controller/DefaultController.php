<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request;

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

    public function loginFormAction(Request $request)
    {
        $form = $this->createForm(new LoginType(), array('email' => $request->get('email')));

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $user = $this->getDoctrine()->getRepository('ActsCamdramBundle:User')->findByEmailAndPassword($data['email'], $data['password']);
                if ($user) {
                    $this->get('session')->set('new_local_user_id', $user->getId());
                    return $this->redirect($this->generateUrl('camdram_security_login', array('service' => 'local')));
                }
                else {
                    $form->addError(new \Symfony\Component\Form\FormError('That username and/or password are incorrect'));
                }
            }
        }

        return $this->render('ActsCamdramSecurityBundle:Default:login.html.twig', array('form' => $form->createView()));
    }

    public function connectUsersAction()
    {
        $token = $this->get('security.context')->getToken();
        $name = $token->getLastService()->getUserInfo('name');
        if (!$name) return $this->redirect($this->generateUrl('camdram_security_no_existing_user'));

        $possible_users = $this->getDoctrine()->getRepository('ActsCamdramBundle:User')
            ->findUsersWithSimilarName($name);

        $possible_users = $this->get('camdram.security.name_utils')
                ->filterPossibleUsers($name, $possible_users);

        if (count($possible_users) == 0) {
            return $this->redirect($this->generateUrl('camdram_security_no_existing_user'));
        }

        if ($this->getRequest()->getMethod() == 'POST') {
            $user_ids = array_keys($this->getRequest()->get('link_users', array()));
            $users = array();
            foreach ($user_ids as $id)
            {
                foreach ($possible_users as $u) {
                    if ($u->getId() == $id) $users[] = $u;
                }
            }

            if (count($users) == 0) return $this->redirect($this->generateUrl('camdram_security_no_existing_user'));
            else {
                $token->setPotentialUsers($users);
                return $this->redirect($this->generateUrl('camdram_security_connect_users_process'));
            }
        }
        else {
            return $this->render('ActsCamdramSecurityBundle:Default:connect_users.html.twig',
                array(
                    'service_name' => $token->getLastService()->getName(),
                    'name' => $name,
                    'possible_users' => $possible_users
                )
            );
        }
    }

    public function connectUsersProcessAction()
    {
        $token = $this->get('security.context')->getToken();
        $users = $token->getPotentialUsers();
        if (!$users) return $this->redirect($this->generateUrl('camdram_security_no_existing_user'));

        $user_status = array();
        $picked_next_user = false;
        $provider = $this->get('camdram.security.user.provider');

        foreach ($users as &$user) {
            $user = $provider->refreshUser($user);

            if ($token->isUserValidated($user)) {
                $user_status[$user->getId()] = 'validated';
            }
            elseif ($picked_next_user == false) {
                $user_status[$user->getId()] = 'next';
                $picked_next_user = true;
            }
            else {
                $user_status[$user->getId()] = 'pending';
            }
        }

        if ($picked_next_user == false) {

            $user = $this->mergeUsers($users);
            foreach ($token->getServices() as $service) {
                $this->addIdentity($user, $service);
            }

            $token->setPotentialUsers(array());
            $token->setUser($user);
            $token->setAuthenticated(true);
            $token->cleanIdentities();

            return $this->redirect($this->generateUrl('camdram_security_login', array('service' => 'complete')));
        }

        return $this->render('ActsCamdramSecurityBundle:Default:connect_users_process.html.twig', array(
            'user_status' => $user_status,
            'users' => $users
        ));
    }

    private function mergeUsers(array $users)
    {
        $keep = array_pop($users);
        $provider = $this->get('camdram.security.user.provider');
        foreach ($users as $user) {
            $provider->mergeUsers($keep, $user);
        }
        return $keep;
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
