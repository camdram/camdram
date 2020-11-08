<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Acts\CamdramSecurityBundle\Form\Type\ChangeEmailType;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Service\TokenGenerator;
use Acts\CamdramSecurityBundle\Service\EmailDispatcher;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Class AccountController
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class AccountController extends AbstractFOSRestController
{
    /**
     * @Route("/account.{_format}", format="html", methods={"GET"}, name="get_account")
     */
    public function getAction(Request $request, AuthorizationCheckerInterface $auth)
    {
        if ($request->getRequestFormat() == 'html') {
            return $this->render('account/settings.html.twig');
        }
        $context = new Context();
        $serializationGroups = ['all'];
        if ($auth->isGranted('ROLE_USER_EMAIL')) {
            $serializationGroups[] = 'user_email';
        }
        $context->setGroups($serializationGroups);

        return $this->view($this->getUser())->setContext($context);
    }

    /**
     * The linked accounts block on the account settings page.
     */
    public function linkedAccountsAction(): Response
    {
        return $this->render('account/linked_accounts.html.twig');
    }

    /**
     * @Security("is_granted('ROLE_USER_SHOWS')")
     * @Route("/account/shows.{_format}", methods={"GET"}, name="get_account_shows")
     */
    public function getShowsAction(AclProvider $aclProvider)
    {
        $shows = $aclProvider->getEntitiesByUser($this->getUser(), 'Acts\\CamdramBundle\\Entity\\Show');

        return $this->view($shows);
    }

    /**
     * @Security("is_granted('ROLE_USER_ORGS')")
     *
     * @Route("/account/organisations.{_format}", methods={"GET"}, name="get_account_organisations")
     */
    public function getOrganisationsAction(AclProvider $aclProvider)
    {
        return $this->view(array_merge(
            $aclProvider->getEntitiesByUser($this->getUser(), 'Acts\\CamdramBundle\\Entity\\Society'),
            $aclProvider->getEntitiesByUser($this->getUser(), 'Acts\\CamdramBundle\\Entity\\Venue')));

    }

    /**
     * @Route("/settings/change-email", methods={"POST"}, name="change_account_email")
     */
    public function changeEmailAction(Request $request): Response
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
     * @Route("/settings/resend-verification", methods={"POST"}, name="resend_account_verification")
     */
    public function resendVerificationAction(TokenGenerator $tokenGenerator, EmailDispatcher $emailDispatcher): Response
    {
        $user = $this->getUser();
        $token = $tokenGenerator->generateEmailConfirmationToken($user);
        $emailDispatcher->resendEmailVerifyEmail($user, $token);

        return $this->redirect($this->generateUrl('get_account'));
    }

    /**
     * @Route("/settings/unlink-account/{service}", methods={"POST"}, name="unlink_account_account")
     */
    public function unlinkAccountAction($service): Response
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

    /**
     * @Route("/account/authorizations/{id}/revoke", name="revoke_account_authorization", methods={"DELETE"})
     */
    public function revokeAuthorization($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(\Acts\CamdramApiBundle\Entity\Authorization::class);
        $auth = $repo->findOneByClientId($this->getUser(), $id);
        if ($auth) {
            $em->remove($auth);
            $em->flush();
        }

        return $this->redirectToRoute('get_account');
    }
}
