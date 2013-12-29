<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Event\CamdramEvents;
use Acts\CamdramBundle\Event\EntityEvent;
use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class AbstractRestController
 *
 * This controller is used as a base for show, venue, society and people pages, containing common functionality for
 * viewing, searching, creating, editing and deleting editities.
 *
 * @package Acts\CamdramBundle\Controller
 */

abstract class AbstractRestController extends FOSRestController
{

    /** @var string The fully qualified class name for the entity represented by the class */
    protected $class;

    /** @var string the English word for the entity represented by the class  */
    protected $type;

    /** @var string the plural form of the English word for the entity represented by the class  */
    protected $type_plural;

    /** @var string the Sphinx index that contins that should be searched for this sort of entity  */
    protected $search_index = 'entity';

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
     *
     * @return null
     */
    protected function checkAuthenticated()
    {

    }

    /**
     * Called immediately prior to saving or updating an entity.
     *
     * @param $entity mixed the entity that is about to be saved
     * @param null|mixed $oldEntity the entity, prior to having its changes applied,
     *      if applicable.
     */
    protected function preSave($entity, $oldEntity=null)
    {

    }

    /**
     * Returns the route parameters used to identity a particular entity. Used when generating URLs for redirects
     *
     * @param $entity the entity to use
     * @return array the parameters to pass to the router
     */
    protected function getRouteParams($entity)
    {
        return array('identifier' => $entity->getSlug());
    }

    /**
     * Load an entity given its identifier (normally its slug, but this method could be overridden to use a different
     * parameter).
     *
     * @param $identifier the identifier given (normally as part of the URL)
     * @return mixed The entity object corresponding to the identifier
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
     * @return Symfony\Component\Form\AbstractType
     */
    abstract protected function getForm($entity = null);

    /**
     * Action for URL e.g. /shows/new
     */
    public function newAction()
    {
        $this->checkAuthenticated();
        $this->get('camdram.security.acl.helper')->ensureGranted('CREATE', new ClassIdentity($this->class));

        $form = $this->getForm();
        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':new.html.twig');
    }


    /**
     * Action where POST request is submitted from new entity form
     */
    public function postAction(Request $request)
    {
        $this->checkAuthenticated();
        $this->get('camdram.security.acl.helper')->ensureGranted('CREATE', new ClassIdentity($this->class));

        $form = $this->getForm();
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->preSave($form->getData());
            $em->persist($form->getData());
            $em->flush();
            $this->get('camdram.security.acl.provider')->grantAccess($form->getData(), $this->getUser(), $this->getUser());
            $this->get('event_dispatcher')->dispatch(CamdramEvents::ENTITY_CREATED, new EntityEvent($form->getData(), $this->getUser()));
            return $this->routeRedirectView('get_'.$this->type, $this->getRouteParams($form->getData()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:'.$this->getController().':new.html.twig');
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

        $form = $this->getForm($entity);
        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':edit.html.twig');
    }

    /**
     * Action where PUT request is submitted from edit entity form
     */
    public function putAction(Request $request, $identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $entity);

        $form = $this->getForm($entity);

        $form->bind($request);
        if ($form->isValid()) {
            $this->preSave($form->getData(), $entity);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView('get_'.$this->type, $this->getRouteParams($form->getData()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:'.$this->getController().':edit.html.twig');
        }
    }

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
     * If a search term 'q' is provided, then a text search is performed against Sphinx. Otherwise, a paginated
     * collection of all entities is returned.
     */
    public function cgetAction(Request $request)
    {
        $this->checkAuthenticated();
        if ($request->get('q')) {
            /** @var $search_provider \Acts\CamdramBundle\Service\Search\ProviderInterface */
            $search_provider = $this->get('acts.camdram.search_provider');

            //Hack to add to allow filtering by, e.g. show within the entity Sphinx index
            $filters = $this->search_index == 'entity' && $this->type
                    ? array('entity_type' => $this->type) : array();

            //If the additional 'autocomplete' parameter is set, then we only return a few results, and prefixes are
            //matched instead of whole words. Used by the global search bar.
            if ($request->query->has('autocomplete')) {
                $data = $search_provider->executeAutocomplete($this->search_index, $request->get('q'), $request->get('limit'), $filters);
            }
            else {
                $data = $search_provider->executeTextSearch($this->search_index, $request->get('q'), $filters);
            }
        }
        else {
            $repo = $this->getRepository();
            $qb = $repo->createQueryBuilder('e');
            $adapter = new DoctrineORMAdapter($qb);
            $data = new Pagerfanta($adapter);
        }

        $view = $this->view($data, 200)
            ->setTemplateVar('result')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':index.html.twig')
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
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':show.html.twig')
            ->setTemplateVar($this->type)
        ;

        return $view;
    }

}
