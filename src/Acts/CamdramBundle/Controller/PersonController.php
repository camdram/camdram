<?php

namespace Acts\CamdramBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Form\Type\PersonType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Exception\AuthenticationException;


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

    public function removeAction($identifier)
    {
	parent::removeAction($identifier);
	return $this->routeRedirectView('acts_camdram_homepage');
    }

    /**
     * * @Rest\Get("/people/{identifier}/link")
     */
    public function linkAction($identifier)
    {
        $person = $this->getEntity($identifier);
        if (!$this->getUser()) {
            throw new AuthenticationException();
        }
        $name_utils = $this->get('camdram.security.name_utils');
        if (true || $name_utils->isSamePerson($this->getUser()->getName(), $person->getName())) {
            $this->getUser()->setPerson($person);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirect($this->generateUrl('get_person', array('identifier' => $identifier)));
    }

    /**
     * @param $identifier
     * @return $this
     * @Rest\Get("/people/{identifier}/past-roles")
     */
    public function getPastRolesAction($identifier)
    {
        $now = $this->get('acts.time_service')->getCurrentTime();
        $person = $this->getEntity($identifier);
        $roles = $this->getDoctrine()->getRepository('ActsCamdramBundle:Role')->getPastByPerson($now, $person);

        $data = array('person' => $person, 'roles' => $roles);
        return $this->view($data, 200)
            ->setTemplateVar('data')
            ->setTemplate('ActsCamdramBundle:Person:past-shows.html.twig');
    }

    /**
     * @param $identifier
     * @return $this
     * @Rest\Get("/people/{identifier}/upcoming-roles")
     */
    public function getUpcomingRolesAction($identifier)
    {
        $now = $this->get('acts.time_service')->getCurrentTime();
        $person = $this->getEntity($identifier);
        $roles = $this->getDoctrine()->getRepository('ActsCamdramBundle:Role')->getUpcomingByPerson($now, $person);

        $data = array('person' => $person, 'roles' => $roles);
        return $this->view($data, 200)
            ->setTemplateVar('data')
            ->setTemplate('ActsCamdramBundle:Person:upcoming-shows.html.twig');
    }

    /**
     * @param $identifier
     * @return $this
     * @Rest\Get("/people/{identifier}/current-roles")
     */
    public function getCurrentRolesAction($identifier)
    {
        $now = $this->get('acts.time_service')->getCurrentTime();
        $person = $this->getEntity($identifier);
        $roles = $this->getDoctrine()->getRepository('ActsCamdramBundle:Role')->getCurrentByPerson($now, $person);

        $data = array('person' => $person, 'roles' => $roles);

        return $this->view($data, 200)
            ->setTemplateVar('data')
            ->setTemplate('ActsCamdramBundle:Person:current-shows.html.twig');
    }
}
