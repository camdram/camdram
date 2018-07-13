<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Form\Type\PersonType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class PersonController
 *
 * Controller for REST actions for people. Inherits from AbstractRestController.
 *
 * @Rest\RouteResource("Person")
 */
class PersonController extends AbstractRestController
{
    protected $class = 'Acts\\CamdramBundle\\Entity\\Person';

    protected $type = 'person';

    protected $type_plural = 'people';

    protected $search_index = 'person';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Person');
    }

    protected function getForm($person = null, $method = 'POST')
    {
        return $this->createForm(PersonType::class, $person, ['method' => $method]);
    }

    public function removeAction($identifier)
    {
        parent::removeAction($identifier);

        return $this->routeRedirectView('acts_camdram_homepage');
    }

    public function getAction($identifier)
    {
        $person = $this->getEntity($identifier);

        //If person is mapped to a different person, redirect to the canonical person
        if ($person->getMappedTo()) {
            return $this->redirectToRoute(
                'get_person',
                array('identifier' => $person->getMappedTo()->getSlug()),
                301
            );
        }

        return parent::getAction($identifier);
    }

    /**
     * Action that allows querying by id. Redirects to slug URL
     * 
     * @Rest\Get("/people/by-id/{id}")
     */
    public function getByIdAction(Request $request, $id)
    {
        $this->checkAuthenticated();
        $person = $this->getRepository()->findOneById($id);
        
        if (!$person)
        {
            throw $this->createNotFoundException('That person id does not exist');
        }

        return $this->redirectToRoute('get_person', ['identifier' => $person->getSlug(), '_format' => $request->getRequestFormat()]);
    }

    /**
     * * @Rest\Get("/people/{identifier}/link")
     */
    /*public function linkAction($identifier)
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
    }*/

    /**
     * @param $identifier
     *
     * @return $this
     * @Rest\Get("/people/{identifier}/past-roles")
     */
    public function getPastRolesAction($identifier)
    {
        $now = new \DateTime;
        $person = $this->getEntity($identifier);
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getPastByPerson($now, $person);

        $data = array('person' => $person, 'shows' => $shows);

        return $this->view($data, 200)
            ->setTemplateVar('data')
            ->setTemplate('person/past-shows.html.twig');
    }

    /**
     * @param $identifier
     *
     * @return $this
     * @Rest\Get("/people/{identifier}/upcoming-roles")
     */
    public function getUpcomingRolesAction($identifier)
    {
        $now = new \DateTime;
        $person = $this->getEntity($identifier);
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getUpcomingByPerson($now, $person);

        $data = array('person' => $person, 'shows' => $shows);

        return $this->view($data, 200)
            ->setTemplateVar('data')
            ->setTemplate('person/upcoming-shows.html.twig');
    }

    /**
     * @param $identifier
     *
     * @return $this
     * @Rest\Get("/people/{identifier}/current-roles")
     */
    public function getCurrentRolesAction($identifier)
    {
        $now = new \DateTime;
        $person = $this->getEntity($identifier);
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getCurrentByPerson($now, $person);

        $data = array('person' => $person, 'shows' => $shows);

        return $this->view($data, 200)
            ->setTemplateVar('data')
            ->setTemplate('person/current-shows.html.twig');
    }

    public function getMergeAction($identifier)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_ADMIN');
        $person = $this->getEntity($identifier);

        return $this->render('person/merge.html.twig', array(
            'person' => $person,
            'form' => $this->get('acts_camdram_admin.people_merger')->createForm()->createView()
        ));
    }

    /**
     * @param $identifier
     * @param $request Request
     *
     * @return $this
     * @Rest\Post("/people/{identifier}/merge")
     */
    public function mergeAction($identifier, Request $request)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_ADMIN');
        $person = $this->getEntity($identifier);
        $merger = $this->get('acts_camdram_admin.people_merger');

        $form = $merger->createForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            if (($otherPerson = $merger->getPersonFromFormData($data))) {
                if ($otherPerson == $person) {
                    $form->addError(new FormError('You cannot map a person to itself'));
                } else {
                    $newPerson = $merger->mergePeople($person, $otherPerson, $data['keep_person'] == 'this');

                    return $this->redirectToRoute('get_person', array('identifier' => $newPerson->getSlug()));
                }
            } else {
                $form->addError(new FormError('Person not found'));
            }
        }

        return $this->render('person/merge.html.twig', array(
            'person' => $person,
            'form' => $form->createView()
        ));
    }
}
