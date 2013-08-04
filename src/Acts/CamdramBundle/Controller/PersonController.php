<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Form\Type\PersonType;


/**
 * Class PersonController
 *
 * Controller for REST actions for people. Inherits from AbstractRestController.
 *
 * @package Acts\CamdramBundle\Controller
 * @RouteResource("Person")
 */
class PersonController extends AbstractRestController
{
    protected $class = 'Acts\\CamdramBundle\\Entity\\Person';

    protected $type = 'person';

    protected $type_plural = 'people';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Person');
    }

    protected function getForm($person = null)
    {
        return $this->createForm(new PersonType(), $person);
    }
}
