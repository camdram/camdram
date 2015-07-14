<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramBundle\Form\Type\RolesType;
use Acts\CamdramBundle\Form\Type\RoleType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class RoleController extends FOSRestController
{
    protected function getEntity($identifier)
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findOneBy(array('slug' => $identifier));
    }

    /**
     * Get a form for adding a single role to a show.
     *
     * @param $identifier
     */
    public function newRoleAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show, false);

        $role = new Role();
        $role->setType($request->query->get('type'));
        $form = $this->createForm(new RoleType(), $role, array(
            'action' => $this->generateUrl('post_show_role', array('identifier' => $identifier))));

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:role-new.html.twig');
    }

    /**
     * Create a new role associated with this show.
     *
     * Creates a new person if they're not already part of Camdram.
     *
     * @param $identifier
     */
    public function postRoleAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $base_role = new Role();
        $form = $this->createForm(new RoleType(), $base_role);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /* Try and find the person. Add a new person if they don't exist. */
            $names = explode(',', $form->get('name')->getData());
            foreach ($names as $name) {
                $role = clone $base_role;
                $name = trim($name);
                $person_repo = $em->getRepository('ActsCamdramBundle:Person');
                $person = $person_repo->findCanonicalPerson($name);
                if ($person == null) {
                    $person = new Person();
                    $person->setName($name);
                    $em->persist($person);
                }
                $role->setPerson($person);
                $order = $this->getDoctrine()->getRepository('ActsCamdramBundle:Role')
                    ->getMaxOrderByShowType($show, $role->getType());
                $role->setOrder(++$order);
                $role->setShow($show);
                $em->persist($role);

                $person->addRole($role);
                $show->addRole($role);
                $em->flush();
            }
        }

        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }

    /**
     * Remove a role from a show.
     */
    public function removeRoleAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('role');
        $role_repo = $em->getRepository('ActsCamdramBundle:Role');
        $role = $role_repo->findOneById($id);
        if ($role != null) {
            $person = $role->getPerson();
            $show->removeRole($role);
            $role_repo->removeRoleFromOrder($role);
            $em->remove($role);
            $em->flush();
            // Ensure the person is not an orphan.
            if ($person->getRoles()->isEmpty()) {
                $em->remove($person);
                $em->flush();
            }
        }

        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }

    /**
     * Get a form for adding multiple roles to a show.
     *
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/many-roles")
     */
    public function getManyRolesAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show, false);

        $form = $this->createForm(new RolesType(), array(
            array('identifier' => $identifier)));

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:roles-new.html.twig');
    }

    /**
     * Process adding multiple roles to a show.
     *
     * This function doesn't do validation of the data, e.g. ensuring
     * that the role and the person aren't reversed, as this is the
     * responsibility of the form validation. This function should be robust
     * against badly formatted input-however.
     *
     * @param $identifier
     * @Rest\Post("/shows/{identifier}/many-roles")
     */
    public function postManyRolesAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show, false);

        $form = $this->createForm(new RolesType(), array(
            array('identifier' => $identifier)));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $role_idx = ($data['ordering'] == 'role_first') ? 0 : 1;
            $name_idx = 1 - $role_idx;
            $lines = explode("\n", $data['list']);
            foreach ($lines as $line) {
                $lsplt = explode($data['separator'], $line);
                /* Ensure the split data contains only the role and the
                 * person's name.
                 */
                if (count($lsplt) != 2) {
                    continue;
                }
                $role = trim($lsplt[$role_idx]);
                $name = trim($lsplt[$name_idx]);
                if (($name != '') && ($role != '')) {
                    /* Add a role to the show. */
                    $this->addRoleToShow(
                        $this->getEntity($identifier),
                        $data['type'],
                        $role,
                        $name
                    );
                }
            }
        }

        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }

    /**
     * Utility function for adding a person to this show. A new person
     * record is created if they don't already exist.
     *
     * @param Show   $show        This show.
     * @param string $role_type   The type of role ('cast', 'band', 'prod')
     * @param string $role_name   Director, Producer, Macbeth..
     * @param string $person_name The person's name
     */
    private function addRoleToShow(Show $show, $role_type, $role_name, $person_name)
    {
        $role = new Role();
        $role->setType($role_type);
        $role->setRole($role_name);

        $em = $this->getDoctrine()->getManager();
        $person_repo = $em->getRepository('ActsCamdramBundle:Person');

        /* Try and find the person. Add a new person if they don't exist. */
        $person = $person_repo->findCanonicalPerson($person_name);
        if ($person == null) {
            $person = new Person();
            $person->setName($person_name);
            $em->persist($person);
        }
        $role->setPerson($person);
        /* Append this role to the list of roles of this type. */
        $order = $this->getDoctrine()->getRepository('ActsCamdramBundle:Role')
            ->getMaxOrderByShowType($show, $role->getType());
        $role->setOrder(++$order);
        $role->setShow($show);
        $em->persist($role);

        $person->addRole($role);
        $show->addRole($role);
        $em->flush();
    }
}
