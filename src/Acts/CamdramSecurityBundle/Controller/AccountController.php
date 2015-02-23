<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Acts\CamdramSecurityBundle\Form\Type\ChangeEmailType;
use Acts\CamdramSecurityBundle\Form\Type\ChangePasswordType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AccountController
 * @package Acts\CamdramSecurityBundle\Controller
 * @RouteResource("Account")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class AccountController extends FOSRestController
{
    public function getAction()
    {
        return $this->view($this->getUser())
            ->setTemplate('ActsCamdramSecurityBundle:Account:settings.html.twig')
            ;
    }

    public function linkedAccountsAction()
    {
        return $this->render('ActsCamdramSecurityBundle:Account:linked_accounts.html.twig', array(
            'services' => $this->get('external_login.service_provider')->getServices()
        ));
    }

    public function getShowsAction()
    {
        $shows = $this->get('camdram.security.acl.provider')->getEntitiesByUser($this->getUser(), 'Acts\\CamdramBundle\\Entity\\Show');
        return $this->view($shows);
    }

    public function getOrganisationsAction()
    {
        $orgs = $this->get('camdram.security.acl.provider')->getEntitiesByUser($this->getUser(), 'Acts\\CamdramBundle\\Entity\\Organisation');
        return $this->view($orgs);
    }

    public function changeEmailAction(Request $request)
    {
        $form = $form = $this->createForm(new ChangeEmailType(), $this->getUser());

        if ($request->getMethod() == 'POST') {
            $form->submit($request);
            if ($form->isValid()) {
                $user = $form->getData();
                $user->setIsEmailVerified(false);
                $this->getDoctrine()->getManager()->flush();
                return $this->render('ActsCamdramSecurityBundle:Account:change_email_complete.html.twig');
            }
            return $this->render('ActsCamdramSecurityBundle:Account:change_email.html.twig', array(
                'form' => $form->createView()
            ));
        }

        return $this->render('ActsCamdramSecurityBundle:Account:change_email_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function changePasswordAction(Request $request)
    {
        $form = $form = $this->createForm(new ChangePasswordType(), array());
        if ($request->getMethod() == 'POST') {
            $form->submit($request);
            if ($form->isValid()) {
                $user = $this->getUser();
                $data = $form->getData();

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($data['password'], $user->getSalt());
                $user->setPassword($password);

                $this->getDoctrine()->getManager()->flush();

                return $this->render('ActsCamdramSecurityBundle:Account:change_password_complete.html.twig');
            }
            return $this->render('ActsCamdramSecurityBundle:Account:change_password.html.twig', array(
                'form' => $form->createView()
            ));
        }

        return $this->render('ActsCamdramSecurityBundle:Account:change_password_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function resendVerificationAction()
    {
        $user = $this->getUser();
        $token = $this->get('camdram.security.token_generator')->generateEmailConfirmationToken($user);
        $this->get('camdram.security.email_dispatcher')->resendEmailVerifyEmail($user, $token);

        return $this->redirect($this->generateUrl('acts_camdram_security_settings'));
    }

    public function unlinkAccountAction($service)
    {
        $user = $this->getUser();
        $external_user = $user->getExternalUserByService($service);

        if ($external_user) {
            $user->removeExternalUser($external_user);
            $em = $this->getDoctrine()->getManager();
            $em->remove($external_user);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('acts_camdram_security_settings'));
    }

}
