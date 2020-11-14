<?php

namespace Acts\CamdramAdminBundle\Controller;

use Acts\CamdramAdminBundle\Form\Type\UserType;
use Acts\CamdramAdminBundle\Service\UserMerger;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_SUPER_ADMIN') and is_granted('IS_AUTHENTICATED_FULLY')")
 */
class UserController extends AbstractController
{
    private function getEntity(int $identifier): User
    {
        $entity = $this->getRepository()->findOneBy(['id' => $identifier]);

        if (!$entity) {
            throw $this->createNotFoundException('That user does not exist');
        }

        return $entity;
    }

    private function getRepository(): \Acts\CamdramSecurityBundle\Entity\UserRepository
    {
        return $this->getDoctrine()->getManager()->getRepository(User::class);
    }

    /**
     * Action which returns a list of entities.
     *
     * If a search term 'q' is provided, then a text search is performed. Otherwise, a paginated
     * collection of all entities is returned.
     * @Route("/users", methods={"GET"}, name="get_users")
     */
    public function cgetAction(Request $request): Response
    {
        $repo = $this->getRepository();
        $page = max(1, (int)($request->query->get('p', '1')));
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

        return $this->render('admin/user/index.html.twig', [
            'paginator' => new Paginator($qb->getQuery()),
            'page_num' => $page,
            'page_urlprefix' => explode('?', $request->getRequestUri())[0] .
                 '?sort='.$sort . '&order='.$order . '&q='.urlencode($q).'&p=',
            'query' => $q
            ]);
    }

    /**
     * @Route("/users/{identifier}", methods={"GET"}, name="get_user")
     */
    public function getAction(AclProvider $aclProvider, int $identifier): Response
    {
        $entity = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('EDIT', $entity);
        $orgs = $aclProvider->getOrganisationsByUser($entity);
        $ids = $aclProvider->getEntitiesByUser($entity, Show::class);
        $shows = $this->getDoctrine()->getRepository(Show::class)->findIdsByDate($ids);
        return $this->render('admin/user/show.html.twig', [
            'user' => $entity,
            'organisations' => $orgs,
            'shows' => $shows
        ]);
    }

    /**
     * @Route("/users/{identifier}/edit", methods={"GET"}, name="edit_user")
     */
    public function editAction(int $identifier): Response
    {
        $entity = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('EDIT', $entity);

        $form = $this->createForm(UserType::class, $entity, ['method' => 'PUT']);

        return $this->render('admin/user/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{identifier}", methods={"PUT"}, name="put_user")
     */
    public function putAction(Request $request, int $identifier): Response
    {
        $entity = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('EDIT', $entity);

        $form = $this->createForm(UserType::class, $entity, ['method' => 'PUT']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('get_user', ['identifier' => $form->getData()->getId()]);
        } else {
            return $this->render('admin/user/edit.html.twig', ['form' => $form->createView()])
                ->setStatusCode(400);
        }
    }

    /**
     * @Route("/users/{identifier}", methods={"DELETE"}, name="delete_user")
     */
    public function deleteAction(Request $request, int $identifier): Response
    {
        $entity = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('DELETE', $entity);

        if (!$this->isCsrfTokenValid('delete_user', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirectToRoute('get_users');
    }

    /**
     * @Route("/users/{identifier}/merge", methods={"GET"}, name="get_user_merge")
     */
    public function getMergeAction(int $identifier, UserMerger $merger): Response
    {
        $user = $this->getEntity($identifier);

        return $this->render('admin/user/merge.html.twig', [
            'user' => $user,
            'form' => $merger->createForm(true)->createView()
        ]);
    }

    /**
     * @Route("/users/{identifier}/merge", methods={"PATCH"}, name="merge_user")
     */
    public function mergeAction(int $identifier, Request $request, UserMerger $merger): Response
    {
        $user = $this->getEntity($identifier);

        $form = $merger->createForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $otherUser = $this->getDoctrine()->getManager()->getRepository(User::class)
                ->findOneByEmail($data['email']);
            if ($otherUser) {
                if ($otherUser == $user) {
                    $form->addError(new FormError('You cannot merge a user with itself'));
                } else {
                    $newUser = $merger->mergeUsers($user, $otherUser, $data['keep_user'] == 'this');

                    return $this->redirectToRoute('get_user', ['identifier' => $newUser->getId()]);
                }
            } else {
                $form->addError(new FormError('User not found'));
            }
        }

        return $this->render('admin/user/merge.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
