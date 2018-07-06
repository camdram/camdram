<?php

namespace Acts\CamdramSecurityBundle\Controller;

use FOS\RestBundle\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramSecurityBundle\Form\Type\ChangeEmailType;
use Acts\CamdramSecurityBundle\Form\Type\ChangePasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AccountController
 *
 * @RouteResource("Account")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class AccountController extends FOSRestController
{
    public function getAction()
    {
        $context = new Context();
        $auth = $this->get('security.authorization_checker');
        $serializationGroups = ['all'];
        if ($auth->isGranted('ROLE_USER_EMAIL') || $auth->isGranted('IS_AUTHENTICATED_FULLY')) {
            $serializationGroups[] = 'user_email';
        }
        $context->setGroups($serializationGroups);
        
        return $this->view($this->getUser())
            ->setTemplate('ActsCamdramSecurityBundle:Account:settings.html.twig')
            ->setContext($context)
            ;
    }

    public function linkedAccountsAction()
    {
        return $this->render('ActsCamdramSecurityBundle:Account:linked_accounts.html.twig');
    }

    /**
     * @Security("has_role('ROLE_USER_SHOWS')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getShowsAction()
    {
        $shows = $this->get('camdram.security.acl.provider')->getEntitiesByUser($this->getUser(), 'Acts\\CamdramBundle\\Entity\\Show');

        return $this->view($shows);
    }

    /**
     * @Security("has_role('ROLE_USER_ORGS')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getOrganisationsAction()
    {
        $orgs = $this->get('camdram.security.acl.provider')->getEntitiesByUser($this->getUser(), 'Acts\\CamdramBundle\\Entity\\Organisation');

        return $this->view($orgs);
    }

    public function changeEmailAction(Request $request)
    {
        $form = $form = $this->createForm(ChangeEmailType::class, $this->getUser());

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
        if (!$this->getUser()->getPassword()) {
            //Adding password only allowed if account already has a password
            return new Response('', 200);
        }

        $form = $form = $this->createForm(ChangePasswordType::class, $this->getUser());
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $user = $form->getData();

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
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

        return $this->redirect($this->generateUrl('get_account'));
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

        return $this->redirect($this->generateUrl('get_account'));
    }
}
