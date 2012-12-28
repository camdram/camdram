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

/**
 * @RouteResource("Entity")
 */
class EntityController extends FOSRestController
{

    public function getAction($slug)
    {
        $entity = $this->getEntity($slug);

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

    public function toolbarAction(Request $request)
    {
        $entity = null;
        if ($request->query->has('slug')) {
            $entity = $this->getEntity($request->get('slug'));
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
    protected function getEntity($slug)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Entity');

        $entity = $repo->findOneBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('That entity does not exist');
        }

        return $entity;
    }

    protected function getUserObj($id)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:User');

        $entity = $repo->findOneById($id);

        if (!$entity) {
            throw $this->createNotFoundException('That user does not exist');
        }

        return $entity;
    }
}
