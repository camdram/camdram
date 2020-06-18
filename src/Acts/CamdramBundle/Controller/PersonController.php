<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramAdminBundle\Service\PeopleMerger;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Form\Type\PersonType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class PersonController
 *
 * Controller for REST actions for people. Inherits from AbstractRestController.
 * @Route("/people")
 */
class PersonController extends AbstractRestController
{
    protected $class = Person::class;

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

    /**
     * @Route("/{identifier}.{_format}", format="html", methods={"GET"}, name="get_person")
     */
    public function getAction($identifier)
    {
        $person = $this->getEntity($identifier);

        //If person is mapped to a different person, redirect to the canonical person
        if ($person->getMappedTo()) {
            return $this->redirectToRoute('get_person',
                ['identifier' => $person->getMappedTo()->getSlug()], 301);
        }

        return parent::doGetAction($person);
    }

    /**
     * People are created by adding them to shows. No form.
     */
    public function newAction() { throw $this->createNotFoundException(); }

    /**
     * Action that allows querying by id. Redirects to slug URL
     *
     * @Route("/by-id/{id}.{_format}", format="html", methods={"GET"}, name="get_person_by_id")
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
     * We don't want the default behaviour of paginated results - output an
     * interesting selection unless there's a query parameter specified.
     * @Route(".{_format}", format="html", methods={"GET"}, name="get_people")
     */
    public function cgetAction(Request $request)
    {
        if ($request->query->has('q')) {
            return $this->entitySearch($request);
        }

        $now = new \Datetime;
        $day = $now->format('N');
        if ($day == 7) {
            $day = 0;
        }
        $interval = new \DateInterval('P'.$day.'DT'.$now->format('H\\Hi\\Ms\\S'));
        $start = $now->sub($interval);
        $end = clone $start;
        $end->add(new \DateInterval('P7D'));

        $selectedPeople = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Person')
            ->getPeopleInDateRange($start, $end, 14);
        return $this->show('person/index.html.twig', 'selectedPeople', $selectedPeople);
    }

    /**
     * * @Rest\Get("/{identifier}/link")
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
     * @Route("/{identifier}/roles.{_format}", format="html", methods={"GET"}, name="get_person_roles")
     */
    public function getRolesAction($identifier)
    {
        $person = $this->getEntity($identifier);
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getByPerson($person);
        $data = [];
        foreach($shows as $show) {
            foreach($show->getRolesByPerson($person) as $role) {
                $data[] = $role;
            }
        }

        return $this->view($data, 200);
    }

    /**
     * @Route("/{identifier}/past-roles.{_format}", format="html", methods={"GET"}, name="get_person_past_roles")
     */
    public function getPastRolesAction($identifier)
    {
        $now = new \DateTime;
        $person = $this->getEntity($identifier);
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getPastByPerson($now, $person);

        $data = array('person' => $person, 'shows' => $shows);

        return $this->show('person/past-shows.html.twig', 'data', $data);
    }

    /**
     * @Route("/{identifier}/upcoming-roles.{_format}", format="html", methods={"GET"}, name="get_person_upcoming_roles")
     */
    public function getUpcomingRolesAction($identifier)
    {
        $now = new \DateTime;
        $person = $this->getEntity($identifier);
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getUpcomingByPerson($now, $person);

        $data = array('person' => $person, 'shows' => $shows);

        return $this->show('person/upcoming-shows.html.twig', 'data', $data);
    }

    /**
     * @Route("/{identifier}/current-roles.{_format}", format="html", methods={"GET"}, name="get_person_current_roles")
     */
    public function getCurrentRolesAction($identifier)
    {
        $now = new \DateTime;
        $person = $this->getEntity($identifier);
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getCurrentByPerson($now, $person);

        $data = array('person' => $person, 'shows' => $shows);

        return $this->show('person/current-shows.html.twig', 'data', $data);
    }

    /**
     * @Route("/{identifier}/edit-roles", methods={"GET"}, name="get_person_edit_roles")
     */
    public function getEditRolesAction($identifier)
    {
        $person = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $person);

        $roles = $this->getDoctrine()->getManager()->createQuery(
            'SELECT r, s FROM ActsCamdramBundle:Role r JOIN r.show s WHERE r.person = :p ORDER BY s.id')
            ->setParameter('p', $person)->getResult();
        $roles = array_filter($roles, function($r) {
            return $this->get('camdram.security.acl.helper')->isGranted('VIEW', $r->getShow());
        });

        return $this->render('person/edit-roles.html.twig', array(
            'person' => $person,
            'roles' => $roles
        ));
    }

    /**
     * @Route("/{identifier}/merge", methods={"GET"}, name="get_person_merge")
     */
    public function getMergeAction($identifier, PeopleMerger $merger)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_ADMIN');
        $person = $this->getEntity($identifier);

        return $this->render('person/merge.html.twig', array(
            'person' => $person,
            'form' => $merger->createForm()->createView()
        ));
    }

    /**
     * @Route("/{identifier}/merge", methods={"POST"}, name="merge_person")
     */
    public function mergeAction($identifier, Request $request, PeopleMerger $merger)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_ADMIN');
        $person = $this->getEntity($identifier);

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
