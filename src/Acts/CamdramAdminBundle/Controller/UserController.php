<?php

namespace Acts\CamdramAdminBundle\Controller;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramAdminBundle\Form\Type\UserType;
use Acts\CamdramAdminBundle\Form\Type\AddAclType;
use Acts\CamdramSecurityBundle\Service\EmailDispatcher;
use Acts\CamdramSecurityBundle\Service\TokenGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @RouteResource("User")
 * @Security("has_role('ROLE_SUPER_ADMIN') and is_granted('IS_AUTHENTICATED_FULLY')")
 */
class UserController extends FOSRestController
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

        if ($request->get('q')) {
            $qb = $repo->search($request->get('q'));
        } else {
            $qb = $repo->createQueryBuilder('u');
        }
        if ($request->get('sort')) {
            // Can't use :parameter notation so manually sanitizing the input.
            $direction = $request->get('order') == 'DESC' ? 'DESC' : 'ASC';
            $qb->orderBy('u.' . preg_replace("/[^A-Za-z_]+/", "", $request->get('sort')), $direction);
        } else {
            $qb->orderBy('u.id', 'ASC');
        }
        $adapter = new DoctrineORMAdapter($qb);
        $data = new Pagerfanta($adapter);
        $data->setMaxPerPage(25);

        return $this->view($data, 200)
            ->setTemplateVar('result')
            ->setTemplate('admin/user/index.html.twig')
        ;
    }

    public function getAction($identifier)
    {
        $entity = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('EDIT', $entity);
        $ids = $this->get('camdram.security.acl.provider')->getOrganisationIdsByUser($entity);
        $orgs = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Organisation')->findById($ids);
        $ids = $this->get('camdram.security.acl.provider')->getEntitiesByUser($entity, '\\Acts\\CamdramBundle\\Entity\\Show');
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

    public function removeAction($identifier)
    {
        $entity = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('DELETE', $entity);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->routeRedirectView('get_users');
    }

    public function newAceAction(Request $request, $identifier)
    {
        $form = $this->createForm(AddAclType::class, array('identifier' => $identifier));

        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('admin/user/ace-new-form.html.twig');
    }

    public function postAceAction(Request $request, $identifier)
    {
        $form = $this->createForm(AddAclType::class, array('identifier' => $identifier));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $user = $this->getEntity($identifier);
            $data = $form->getData();
            $this->get('camdram.security.acl.provider')->grantAccess($data['entity'], $user, $this->getUser());

            return $this->routeRedirectView('get_'.$this->type, $this->getRouteParams($user));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('user')
                ->setTemplate('admin/user/ace-new.html.twig');
        }
    }

    /**
     * @param $identifier
     * @Get("/users/{identifier}/reset-password")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordAction($identifier, TokenGenerator $tokenGenerator, EmailDispatcher $emailDispatcher)
    {
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

    public function getMergeAction($identifier)
    {
        $user = $this->getEntity($identifier);

        return $this->render('admin/user/merge.html.twig', array(
            'user' => $user,
            'form' => $this->get('acts_camdram_admin.user_merger')->createForm()->createView()
        ));
    }

    /**
     * @param $identifier
     * @param $request Request
     *
     * @return $this
     */
    public function mergeAction($identifier, Request $request)
    {
        $user = $this->getEntity($identifier);
        $merger = $this->get('acts_camdram_admin.user_merger');

        $form = $merger->createForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $otherUser = $this->get('doctrine.orm.entity_manager')->getRepository('ActsCamdramSecurityBundle:User')
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
