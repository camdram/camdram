<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Entity\Entity;

use FOS\RestBundle\Controller\Annotations\NoRoute;

/**
 * @RouteResource("Entity")
 */
class EntityController extends FOSRestController
{

    public function getAction($id)
    {
        $entity = $this->getEntity($id);

        if ($entity instanceof Show) {
            $route = 'get_show';
        }
        elseif ($entity instanceof  Person) {
            $route = 'get_person';
        }
        elseif ($entity instanceof Society) {
            $route = 'get_society';
        }
        elseif ($entity instanceof Venue) {
            $route = 'get_venue';
        }
        else {
            throw $this->createNotFoundException('Entity has an invalid type');
        }

        return $this->redirect($this->generateUrl($route, array('slug' => $entity->getSlug())));
    }

    public function cgetAction(Request $request)
    {
        /** @var $search_provider \Acts\CamdramBundle\Service\Search\ProviderInterface */
        $search_provider = $this->get('acts.camdram.search_provider');


        if ($request->query->has('autocomplete')) {
            $data = $search_provider->executeAutocomplete('entity', $request->get('q'), $request->get('limit'));
        }
        else {
            $data = $search_provider->executeTextSearch('entity', $request->get('q'));
        }

        return $this->view($data, 200)
            ->setTemplate('ActsCamdramBundle:Entity:index.html.twig')
            ->setTemplateVar('result');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @NoRoute
     */
    public function toolbarAction(Request $request)
    {
        $entity = null;
        if ($request->query->has('id')) {
            $entity = $this->getEntity($request->get('id'));
        }
        if ($request->query->has('type')) {
            $type = $request->get('type');
        }
        else {
            $type = $this->getEntityType($entity);
        }
        if ($type == 'user') {
            $entity = $this->getUserObj($request->get('id'));
        }

        if ($entity) {
            $routes = array(
                'edit' => 'edit_'.$type,
                'new' => 'new_'.$type,
                'delete' => 'remove_'.$type,
            );
        }
        else {
            $routes = array('new' => 'new_'.$type);
        }
        $label = $type;

        return $this->render('ActsCamdramBundle:Entity:toolbar.html.twig', array(
            'routes' => $routes,
            'entity' => $entity,
            'label' => $label,
            'type' => $type,
        ));
    }

    /**
     * @param $entity
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getEntityType($entity)
    {
        if ($entity instanceof Show) {
            $type = 'show';
        }
        elseif ($entity instanceof  Person) {
            $type = 'person';
        }
        elseif ($entity instanceof Society) {
            $type = 'society';
        }
        elseif ($entity instanceof Venue) {
            $type = 'venue';
        }
        elseif ($entity instanceof User) {
            $type = 'user';
        }
        else {
            throw $this->createNotFoundException('Entity has an invalid type');
        }
        return $type;
    }

    /**
     * @return \Acts\CamdramBundle\Entity\Entity;
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Entity');

        $entity = $repo->findOneById($id);

        if (!$entity) {
            throw $this->createNotFoundException('That entity does not exist');
        }

        return $entity;
    }

}
