<?php

namespace Acts\CamdramBundle\Controller;

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
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Entity');

        $entity = $repo->findOneBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('That entity does not exist');
        }

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
}
