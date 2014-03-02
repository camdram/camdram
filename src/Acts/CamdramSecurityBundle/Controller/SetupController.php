<?php

namespace Acts\CamdramSecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request;

use Acts\CamdramSecurityBundle\Form\Type\LoginType,
    Acts\CamdramSecurityBundle\Form\Type\UserType,
    Acts\CamdramSecurityBundle\Entity\UserIdentity,
    Acts\CamdramSecurityBundle\Security\Authentication\Token\CamdramUserTokenService,
    Acts\CamdramSecurityBundle\Entity\User,
    Acts\CamdramBundle\Entity\Person;

class SetupController extends Controller
{

    public function defaultAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if (is_null($user->getPerson())) {
            $this->redirect($this->generateUrl('camdram_security_setup_link_person'));
        } else {
            return $this->redirect($this->generateUrl('camdram_security_login', array('service' => 'complete')));
        }
    }

    public function linkPersonAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if ($user->getPerson()) {
            $this->redirect($this->generateUrl('camdram_security_setup_roles'));
        }

        $people_res = $this->getDoctrine()->getRepository('ActsCamdramBundle:Person');
        $people = $people_res->findWithSimilarName($user->getName());
        $utils = $this->get('camdram.security.name_utils');
        $people = $utils->filterPossibleUsers($user->getName(), $people);
        if (count($people) == 0) {
            $this->redirect($this->generateUrl('camdram_security_setup_roles'));
        }

        if ($request->getMethod() == 'POST') {
            $person_ids = array_keys($this->getRequest()->get('link_people', array()));
            $selected_people = array();
            foreach ($person_ids as $id) {
                foreach ($people as $p) {
                    if ($p->getId() == $id) $selected_people[] = $p;
                }
            }
            $em = $this->getDoctrine()->getManager();

            if (count($selected_people) == 0) {
                $p = new Person;
                $p->setName($user->getName());
                $user->setPerson($p);
                $em->persist($p);
            } else {
                $person = $this->mergePeople($people);
                $user->setPerson($person);
            }
            $em->flush();
            return $this->redirect($this->generateUrl('camdram_security_setup'));
        }

        return $this->render('ActsCamdramSecurityBundle:Setup:link_person.html.twig', array(
            'people' => $people
        ));

    }

    private function mergePeople(array $people)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        //Shortcut if only one person
        if (count($people) == 1) return $people[0];

        //Decide which person to keep
        //First, pick one with same name, as this is most likely the preferred name
        $selected_id = null;
        foreach ($people as $id => $person) {
            if ($person->getName() == $user->getName()) {
                $selected_id = $id;
            }
        }
        if (is_null($selected_id)) {
            //Otherwise pick one with the most associated roles
            $max_roles = -1;
            foreach ($people as $id => $person) {
                if (count($person->getRoles()) > $max_roles) {
                    $selected_id = $id;
                    $max_roles = count($person->getRoles());
                }
            }
        }
        $keep = $people[$selected_id];
        $people = array_splice($people,$selected_id,1);

        $em = $this->getDoctrine()->getManager();

        foreach ($people as $person) {
            foreach ($person->getRoles() as $role) {
                $role->setPerson($keep);
            }
            foreach ($person->getUsers() as $user) {
                $user->setPerson($keep);
            }
            $em->remove($person);
        }
        $em->flush();
    }

    public function rolesAction()
    {

    }

}
