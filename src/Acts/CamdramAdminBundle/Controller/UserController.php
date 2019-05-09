<?php

namespace Acts\CamdramAdminBundle\Controller;

use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramAdminBundle\Form\Type\UserType;
use Acts\CamdramAdminBundle\Form\Type\AddAclType;
use Acts\CamdramAdminBundle\Service\UserMerger;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Service\EmailDispatcher;
use Acts\CamdramSecurityBundle\Service\TokenGenerator;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Rest\RouteResource("User")
 * @Security("has_role('ROLE_SUPER_ADMIN') and is_granted('IS_AUTHENTICATED_FULLY')")
 */
class UserController extends AbstractFOSRestController
{
    protected function getRouteParams($user)
    {
        return array('identifier' => $user->getId());
    }

    protected function getEntity($identifier)
    {
        $entity = $this->getRepository()->findOneBy(array('id' => $identifier));

        if (!$entity) {
            throw $this->createNotFoundException('That user does not exist');
        }

        return $entity;
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramSecurityBundle:User');
    }

    /**
     * Action which returns a list of entities.
     *
     * If a search term 'q' is provided, then a text search is performed. Otherwise, a paginated
     * collection of all entities is returned.
     */
    public function cgetAction(Request $request)
    {
        $repo = $this->getRepository();
        $page = (int)($request->query->get('p', 1));
        // Can't use :parameter notation so manually sanitizing the input.
        $sort  = preg_replace('/[^A-Za-z_]+/', '', $request->query->get('sort', 'id'));
        $order = ($request->query->get('order') == 'DESC') ? 'DESC' : 'ASC';
        $q     = $request->get('q', '');

        if ($q !== '') {
            $qb = $repo->search($request->get('q'));
        } else {
            $qb = $repo->createQueryBuilder('u');
        }
        $qb->orderBy('u.'.$sort, $order);
        $qb->setMaxResults(25);
        $qb->setFirstResult(25 * ($page - 1));

        return $this->view([
            'paginator' => new Paginator($qb->getQuery()),
            'page_num' => $page,
            'page_urlprefix' => explode('?', $request->getRequestUri())[0] .
                 '?sort='.$sort . '&order='.$order . '&q='.urlencode($q).'&p=',
            'query' => $q
            ], 200)->setTemplate('admin/user/index.html.twig');
    }

    /**
     * @Rest\Get("/users/{identifier}")
     */
    public function getAction(AclProvider $aclProvider, $identifier)
    {
        $entity = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('EDIT', $entity);
        $ids = $aclProvider->getOrganisationIdsByUser($entity);
        $orgs = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Organisation')->findById($ids);
        $ids = $aclProvider->getEntitiesByUser($entity, '\\Acts\\CamdramBundle\\Entity\\Show');
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findIdsByDate($ids);
        $view = $this->view(array(
            'user' => $entity,
            'organisations' => $orgs,
            'shows' => $shows
            ), 200)
            ->setTemplate('admin/user/show.html.twig')
            ->setTemplateVar('user')
        ;

        return $view;
    }

    public function editAction($identifier)
    {
        $entity = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('EDIT', $entity);

        $form = $this->createForm(UserType::class, $entity, ['method' => 'PUT']);

        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('admin/user/edit.html.twig');
    }

    public function putAction(Request $request, $identifier)
    {
        $entity = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('EDIT', $entity);

        $form = $this->createForm(UserType::class, $entity, ['method' => 'PUT']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->routeRedirectView('get_user', $this->getRouteParams($form->getData()));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('admin/user/edit.html.twig');
        }
    }

    public function deleteAction(Request $request, $identifier)
    {
        $entity = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('DELETE', $entity);

        if (!$this->isCsrfTokenValid('delete_user', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->routeRedirectView('get_users');
    }

    /**
     * @param $identifier
     * @Rest\Patch("/users/{identifier}/reset-password")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordAction(Request $request, $identifier, 
            TokenGenerator $tokenGenerator, EmailDispatcher $emailDispatcher)
    {
        if (!$this->isCsrfTokenValid('reset_user_password', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $user = $this->getEntity($identifier);

        $token = $tokenGenerator->generatePasswordResetToken($user);
        $emailDispatcher->sendPasswordResetEmail($user, $token);
        $url = $this->generateUrl(
            'acts_camdram_security_reset_password',
            array('email' => $user->getEmail(), 'token' => $token),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->render(
            'admin/user/reset-password-complete.html.twig',
              array('user' => $user, 'url' => $url)
        );
    }

    /**
     * @Rest\Get("/users/{identifier}/merge")
     */
    public function getMergeAction($identifier, UserMerger $merger)
    {
        $user = $this->getEntity($identifier);

        return $this->render('admin/user/merge.html.twig', array(
            'user' => $user,
            'form' => $merger->createForm(true)->createView()
        ));
    }

    /**
     * @param $identifier
     * @param $request Request
     *
     * @return $this
     */
    public function mergeAction($identifier, Request $request, UserMerger $merger)
    {
        $user = $this->getEntity($identifier);

        $form = $merger->createForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $otherUser = $this->getDoctrine()->getManager()->getRepository('ActsCamdramSecurityBundle:User')
                ->findOneByEmail($data['email']);
            if ($otherUser) {
                if ($otherUser == $user) {
                    $form->addError(new FormError('You cannot merge a user with itself'));
                } else {
                    $newUser = $merger->mergeUsers($user, $otherUser, $data['keep_user'] == 'this');

                    return $this->redirectToRoute('get_user', array('identifier' => $newUser->getId()));
                }
            } else {
                $form->addError(new FormError('User not found'));
            }
        }

        return $this->render('admin/user/merge.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }
}
