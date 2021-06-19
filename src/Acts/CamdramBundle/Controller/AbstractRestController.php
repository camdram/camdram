<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractRestController
 *
 * This controller is used as a base for show, venue, society and people pages, containing common functionality for
 * viewing, searching, creating, editing and deleting editities.
 * @template T of \Acts\CamdramBundle\Entity\BaseEntity
 */
abstract class AbstractRestController extends AbstractFOSRestController
{
    /** @var class-string<T> The fully qualified class name for the entity represented by the class */
    protected $class;

    /** @var string the English word for the entity represented by the class  */
    protected $type;

    /** @var string the plural form of the English word for the entity represented by the class  */
    protected $type_plural;

    /** @var RequestStack */
    protected $requestStack;

    /** @var EntityManagerInterface */
    protected $em;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack) {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /** @return array<mixed> */
    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services['camdram.security.acl.helper'] = Helper::class;
        $services['camdram.security.acl.provider'] = AclProvider::class;
        $services['logger'] = LoggerInterface::class;
        return $services;
    }

    /**
     * @return string used to populate the namespace of the templates for this class
     */
    protected function getController()
    {
        return ucfirst($this->type);
    }

    /**
     * Returns the route parameters used to identity a particular entity. Used when generating URLs for redirects
     * @param T $entity
     * @return array<string> the parameters to pass to the router
     */
    protected function getRouteParams($entity, Request $request)
    {
        return array('identifier' => $entity->getSlug(), '_format' => $request->getRequestFormat());
    }

    /**
     * Load an entity given its identifier (normally its slug, but this method could be overridden to use a different
     * parameter).
     *
     * @param int|string $identifier the identifier given (normally as part of the URL)
     * @return T
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getEntity($identifier): object
    {
        $entity = $this->getRepository()->findOneBySlug($identifier);

        if (!$entity) {
            throw $this->createNotFoundException('That '.$this->type.' does not exist');
        }

        return $entity;
    }

    /**
     * Return the Doctrine repository corresponding to the entity type represented by the child class.
     * @return \Doctrine\ORM\EntityRepository<T>
     */
    protected function getRepository()
    {
        return $this->em->getRepository($this->class);
    }

    /**
     * Return a Form type object corresponding to the entity type represented by the child class.
     * The Type objects are defined in src\CamdramBundle\Form\Type, which define what a form looks like for each class,
     * including validation rules.
     * @param T $entity
     * @param string $method
     * @return \Symfony\Component\Form\FormInterface
     */
    abstract protected function getForm($entity = null, string $method = 'POST');

    /**
     * @Route("/new", methods={"GET"})
     */
    public function newAction(): Response
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('CREATE', $this->class);

        $form = $this->getForm()->createView();

        return $this->render($this->type.'/new.html.twig', ['form' => $form]);
    }

    /**
     * Action where POST request is submitted from new entity form
     * @Route(".{_format}", format="html", methods={"POST"})
     */
    public function postAction(Request $request): Response
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('CREATE', $this->class);

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->get('camdram.security.acl.provider')->grantAccess($form->getData(), $this->getUser(), $this->getUser());
            $this->afterEditFormSubmitted($form, $form->getData()->getSlug());
            $em->flush();
            return $this->redirectIfHuman('get_'.$this->type, $this->getRouteParams($form->getData(), $request), 201);
        } else {
            return $this->render($this->type.'/new.html.twig', ['form' => $form->createView()])
                ->setStatusCode(400);
        }
    }

    /**
     * Action for URL e.g. /shows/the-lion-king/edit
     * @Route("/{identifier}/edit")
     * @return Response|View
     */
    public function editAction(string $identifier)
    {
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $entity);

        $form = $this->getForm($entity, 'PUT');
        $this->modifyEditForm($form, $identifier);

        return $this->show($this->type.'/edit.html.twig', 'form', $form->createView());
    }

    /**
     * Action where PUT request is submitted from edit entity form
     * @Route("/{identifier}.{_format}", format="html", methods={"PUT"})
     */
    public function putAction(Request $request, string $identifier): Response
    {
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $entity);

        $form = $this->getForm($entity, 'PUT');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->afterEditFormSubmitted($form, $identifier);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectIfHuman('get_'.$this->type, $this->getRouteParams($form->getData(), $request));
        } else {
            return $this->render($this->type.'/edit.html.twig',
                ['form' => $form->createView()])->setStatusCode(400);
        }
    }

    /**
     * Action where PATCH request is submitted from edit entity form
     * @Route("/{identifier}.{_format}", format="html", methods={"PATCH"})
     */
    public function patchAction(Request $request, string $identifier): Response
    {
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $entity);

        $form = $this->getForm($entity);

        $form->submit($request->request->get($form->getName()), true);
        if ($form->isValid()) {
            $this->afterEditFormSubmitted($form, $identifier);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectIfHuman('get_'.$this->type, $this->getRouteParams($form->getData(), $request), 201);
        } else {
            return $this->render($this->type.'/edit.html.twig',
                ['form' => $form->createView()])->setStatusCode(400);
        }
    }

    /**
     * Called before a new edit form is sent to the user.
     */
    public function modifyEditForm($form, string $identifier): void {}

    /**
     * Called after an edit (or new) form has been successfully submitted and
     * changes given to Doctrine.
     * $em->flush() will be called soon after this is called.
     */
    public function afterEditFormSubmitted($form, string $identifier): void {}

    /**
     * Action to delete entity
     * @Route("/{identifier}", methods={"DELETE"})
     */
    public function deleteAction(Request $request, string $identifier): Response
    {
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('DELETE', $entity);
        $name = $entity->getName();

        if (!$this->isCsrfTokenValid('delete_' . $this->type, $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        $this->addFlash('success', "Deleted {$this->type} “{$name}”.");

        return $this->redirectToRoute('get_'.$this->type_plural);
    }

    /**
     * Action which returns a list of entities.
     *
     * If a search term 'q' is provided, then a text search is performed.
     * Otherwise a home page of some sort is shown.
     */
    abstract public function cgetAction(Request $request);

    /**
     * Perform a search.
     * @return Response|View
     */
    protected function entitySearch(Request $request) {
        return $this->forward('Acts\CamdramBundle\Controller\SearchController::search',
            ['types' => [$this->type]]);
    }

    /**
     * Action for pages that represent a single entity - the entity in question is passed to the template
     * @param T $entity
     * @param array<mixed> $extraData
     * @return Response|View
     */
    public function doGetAction($entity, array $extraData = [])
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('VIEW', $entity, false);

        return $this->show($this->type.'/show.html.twig', $this->type, $entity, $extraData);
    }

    /**
     * @return Response|View
     */
    protected function show(string $template, string $templateVar, $data, array $templateData = [])
    {
        $format = $this->requestStack->getCurrentRequest()->getRequestFormat();
        if ($format == 'html' || $format == 'txt') {
            if (is_array($data)) {
                $templateData += $data;
            }
            $templateData[$templateVar] = $data;
            return $this->render($template, $templateData);
        } else {
            return $this->view($data);
        }
    }

    protected function redirectIfHuman(string $route, array $routeParams, int $successCode = 200): Response
    {
        $format = $this->requestStack->getCurrentRequest()->getRequestFormat();
        if ($format == 'html' || $format == 'txt') {
            return $this->redirectToRoute($route, $routeParams);
        } else {
            return new Response('', $successCode);
        }
    }
}
