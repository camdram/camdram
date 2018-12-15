<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Acts\CamdramSecurityBundle\Form\Type\ChangeEmailType;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Service\TokenGenerator;
use Acts\CamdramSecurityBundle\Service\EmailDispatcher;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Class AccountController
 *
 * @Rest\RouteResource("Account")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class AccountController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/account")
     */
    public function getAction(AuthorizationCheckerInterface $auth)
    {
        $context = new Context();
        $serializationGroups = ['all'];
        if ($auth->isGranted('ROLE_USER_EMAIL') || $auth->isGranted('IS_AUTHENTICATED_FULLY')) {
            $serializationGroups[] = 'user_email';
        }
        $context->setGroups($serializationGroups);

        return $this->view($this->getUser())
            ->setTemplate('account/settings.html.twig')
            ->setContext($context)
            ;
    }

    /**
     * The linked accounts block on the account settings page.
     * @Rest\NoRoute()
     */
    public function linkedAccountsAction()
    {
        return $this->render('account/linked_accounts.html.twig');
    }

    /**
     * @Security("has_role('ROLE_USER_SHOWS')")
     * @Rest\Get("/account/shows")
     * @return \FOS\RestBundle\View\View
     */
    public function getShowsAction(AclProvider $aclProvider)
    {
        $shows = $aclProvider->getEntitiesByUser($this->getUser(), 'Acts\\CamdramBundle\\Entity\\Show');

        return $this->view($shows);
    }

    /**
     * @Security("has_role('ROLE_USER_ORGS')")
     *
     * @Rest\Get("/account/organisations")
     * @return \FOS\RestBundle\View\View
     */
    public function getOrganisationsAction(AclProvider $aclProvider)
    {
        return $this->view(array_merge(
            $aclProvider->getEntitiesByUser($this->getUser(), 'Acts\\CamdramBundle\\Entity\\Society'),
            $aclProvider->getEntitiesByUser($this->getUser(), 'Acts\\CamdramBundle\\Entity\\Venue')));

    }

    /**
     * @Rest\Post("/settings/change-email")
     */
    public function changeEmailAction(Request $request)
    {
        $form = $form = $this->createForm(ChangeEmailType::class, $this->getUser());

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $form->getData();
                $user->setIsEmailVerified(false);
                $this->getDoctrine()->getManager()->flush();

                return $this->render('account/change_email_complete.html.twig');
            }

            return $this->render('account/change_email.html.twig', array(
                'form' => $form->createView()
            ));
        }

        return $this->render('account/change_email_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Rest\Post("/settings/resend-verification")
     */
    public function resendVerificationAction(TokenGenerator $tokenGenerator, EmailDispatcher $emailDispatcher)
    {
        $user = $this->getUser();
        $token = $tokenGenerator->generateEmailConfirmationToken($user);
        $emailDispatcher->resendEmailVerifyEmail($user, $token);

        return $this->redirect($this->generateUrl('get_account'));
    }

    /**
     * @Rest\Post("/settings/unlink-account/{service}")
     */
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
