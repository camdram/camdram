<?php

namespace Acts\CamdramBundle\Controller;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Elastica\Query;
use Elastica\Query\MultiMatch;

/**
 * Class AbstractRestController
 *
 * This controller is used as a base for show, venue, society and people pages, containing common functionality for
 * viewing, searching, creating, editing and deleting editities.
 */
abstract class AbstractRestController extends FOSRestController
{
    /** @var string The fully qualified class name for the entity represented by the class */
    protected $class;

    /** @var string the English word for the entity represented by the class  */
    protected $type;

    /** @var string the plural form of the English word for the entity represented by the class  */
    protected $type_plural;

    /**
     * @return string used to populate the namespace of the templates for this class
     */
    protected function getController()
    {
        return ucfirst($this->type);
    }

    /**
     * Called on each page load. Default is to do nothing, but allows child classes to do stricter access checking
     * (e.g. for user administration pages)
     */
    protected function checkAuthenticated()
    {
    }

    /**
     * Returns the route parameters used to identity a particular entity. Used when generating URLs for redirects
     *
     * @param $entity the entity to use
     *
     * @return array the parameters to pass to the router
     */
    protected function getRouteParams($entity, Request $request)
    {
        return array('identifier' => $entity->getSlug(), '_format' => $request->getRequestFormat());
    }

    /**
     * Load an entity given its identifier (normally its slug, but this method could be overridden to use a different
     * parameter).
     *
     * @param $identifier the identifier given (normally as part of the URL)
     *
     * @return mixed The entity object corresponding to the identifier
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getEntity($identifier)
    {
        $entity = $this->getRepository()->findOneBySlug($identifier);

        if (!$entity) {
            throw $this->createNotFoundException('That '.$this->type.' does not exist');
        }

        return $entity;
    }

    /**
     * Return the Doctrine repository corresponding to the entity type represented by the child class.
     */
    abstract protected function getRepository();

    /**
     * Return a Form type object corresponding to the entity type represented by the child class.
     * The Type objects are defined in src\CamdramBundle\Form\Type, which define what a form looks like for each class,
     * including validation rules.
     *
     * @param null $entity
     *
     * @return Symfony\Component\Form\AbstractType
     */
    abstract protected function getForm($entity = null, $method = 'POST');

    /**
     * Action for URL e.g. /shows/new
     */
    public function newAction()
    {
        $this->checkAuthenticated();
        $this->get('camdram.security.acl.helper')->ensureGranted('CREATE', $this->class);

        $form = $this->getForm();

        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate($this->type.'/new.html.twig');
    }

    /**
     * Action where POST request is submitted from new entity form
     */
    public function postAction(Request $request)
    {
        $this->checkAuthenticated();
        $this->get('camdram.security.acl.helper')->ensureGranted('CREATE', $this->class);

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($form->getData());
                $em->flush();
            } catch (\Elastica\Exception\ExceptionInterface $ex) {
                $this->get('logger')->warning('Failed to add new entity to search index', ['type' => $this->type, 'id' => $entity->getId()]);
            }
            $this->get('camdram.security.acl.provider')->grantAccess($form->getData(), $this->getUser(), $this->getUser());
            $this->afterEditFormSubmitted($form, $form->getData()->getSlug());
            $em->flush();
            return $this->routeRedirectView('get_'.$this->type, $this->getRouteParams($form->getData(), $request));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate($this->type.'/new.html.twig');
        }
    }

    /**
     * Action for URL e.g. /shows/the-lion-king/edit
     */
    public function editAction($identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $entity);

        $form = $this->getForm($entity, 'PUT');
        $this->modifyEditForm($form, $identifier);

        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate($this->type.'/edit.html.twig');
    }

    /**
     * Action where PUT request is submitted from edit entity form
     */
    public function putAction(Request $request, $identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $entity);

        $form = $this->getForm($entity, 'PUT');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->afterEditFormSubmitted($form, $identifier);
            $em = $this->getDoctrine()->getManager();
            try {
                $em->flush();
            } catch (\Elastica\Exception\ExceptionInterface $ex) {
                $this->get('logger')->warning('Failed to update search index', ['type' => $this->type, 'id' => $entity->getId()]);
            }
            return $this->routeRedirectView('get_'.$this->type, $this->getRouteParams($form->getData(), $request));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate($this->type.'/edit.html.twig');
        }
    }

    /**
     * Action where PATCH request is submitted from edit entity form
     */
    public function patchAction(Request $request, $identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $entity);

        $form = $this->getForm($entity);

        $form->submit($request->request->get($form->getName()), true);
        if ($form->isValid()) {
            $this->afterEditFormSubmitted($form, $identifier);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView('get_'.$this->type, $this->getRouteParams($form->getData(), $request));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate($this->type.'/edit.html.twig');
        }
    }

    /**
     * Called before a new edit form is sent to the user.
     */
    public function modifyEditForm($form, $identifier) {}

    /**
     * Called after an edit (or new) form has been successfully submitted and
     * changes given to Doctrine.
     * $em->flush() will be called soon after this is called.
     */
    public function afterEditFormSubmitted($form, $identifier) {}

    /**
     * Action where PUT request is submitted from edit entity form
     */
    public function removeAction($identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('DELETE', $entity);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->routeRedirectView('get_'.$this->type_plural);
    }

    /**
     * Action which returns a list of entities.
     *
     * If a search term 'q' is provided, then a text search is performed against Elasticsearch. Otherwise, a paginated
     * collection of all entities is returned.
     */
    public function cgetAction(Request $request)
    {
        $this->checkAuthenticated();

        if (!$request->get('page')) {
            $request->query->set('page', 1);
        }
        if (!$request->get('limit')) {
            $request->query->set('limit', 10);
        }

        if ($request->get('q')) {
            $match = new MultiMatch;
            $match->setQuery($request->get('q'));
            $match->setFields(['name', 'short_name']);

            $page = $request->get('page');
            $limit = $request->get('limit');
            $query = new Query($match);
            $query->setFrom(($page-1)*$limit)->setSize($limit);
            //PHP_INT_MAX used because '_first' triggers an integer overflow in json_decode on 32 bit...
            $query->setSort([
                'rank' => ['order' => 'desc', 'unmapped_type' => 'long', 'missing' => PHP_INT_MAX-1]
            ]);

            $search = $this->get('fos_elastica.index.autocomplete_'.$this->type)->createSearch();
            $resultSet = $search->search($query);

            $data = [];
            foreach ($resultSet as $result) {
                $row = $result->getSource();
                $row['id'] = $result->getId();
                $row['entity_type'] = $result->getType();
                $data[] = $row;
            }
        }
        else {
            $repo = $this->getRepository();
            $qb = $repo->selectAll()->getQuery();
            $adapter = new DoctrineORMAdapter($qb);
            $data = new Pagerfanta($adapter);
        }

        $view = $this->view($data, 200)
            ->setTemplateVar('result')
            ->setTemplate($this->type.'/index.html.twig')
        ;

        return $view;
    }

    /**
     * Action for pages that represent a single entity - the entity in question is passed to the template
     */
    public function getAction($identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('VIEW', $entity, false);
        $view = $this->view($entity, 200)
            ->setTemplate($this->type.'/show.html.twig')
            ->setTemplateVar($this->type)
        ;

        return $view;
    }

    /**
     * Action called by Camdram v1 which triggers creating fields only used by v1, e.g. the slug
     *
     * @param $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function upgradeAction($id)
    {
        $this->checkAuthenticated();
        $entity = $this->getRepository()->findOneById($id);
        if (!$entity) {
            throw new NotFoundHttpException('Not found');
        }

        $em = $this->getDoctrine()->getManager();
        $entity->setSlug('__id__');
        $changeset = array();
        $this->getDoctrine()->getManager()->getEventManager()->dispatchEvent('preUpdate', new PreUpdateEventArgs($entity, $this->getDoctrine()->getManager(), $changeset));
        $em->flush();

        $this->getDoctrine()->getManager()->getEventManager()->dispatchEvent('postUpdate', new LifecycleEventArgs($entity, $this->getDoctrine()->getManager()));

        return $this->view(array('success' => true), 200);
    }
}
