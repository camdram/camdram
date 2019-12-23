<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\RolesType;
use Acts\CamdramBundle\Form\Type\RoleType;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function getEntity($identifier)
    {
        return $this->em->getRepository('ActsCamdramBundle:Show')->findOneBy(array('slug' => $identifier));
    }

    /**
     * Get a form for adding a single role to a show.
     * Not an Action, only rendered as part of another template.
     *
     * @param $identifier
     */
    public function newRole(Request $request, Helper $helper, $identifier, $type)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show, false);

        $role = new Role();
        $role->setType($type);
        $form = $this->createForm(RoleType::class, $role, array(
            'action' => $this->generateUrl('post_show_role', array('identifier' => $identifier))));

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('show/role-new.html.twig');
    }

    /**
     * Create a new role associated with this show.
     *
     * Creates a new person if they're not already part of Camdram.
     *
     * @Rest\Post("/shows/{identifier}/roles")
     * @param $identifier
     */
    public function postRoleAction(Request $request, Helper $helper, LoggerInterface $logger, $autocomplete_person, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $base_role = new Role();
        $form = $this->createForm(RoleType::class, $base_role);
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

                try {
                    $em->flush();

                    //Attempt to update role count in people search index
                    $autocomplete_person->replaceOne($person);
                }
                catch (\Elastica\Exception\ExceptionInterface $ex) {
                    $logger->warning('Failed to update search index during role entry',
                            ['role' => $role->getId()]);
                }
            }
        }

        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }

    /**
     * Add or modify a role and return relevant data for the form.
     *
     * Creates a new person if they're not already part of Camdram.
     *
     * @Rest\Patch("/shows/{identifier}/roles")
     * @param $identifier
     */
    public function patchRoleAction(Request $request, Helper $helper, LoggerInterface $logger, $autocomplete_person, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        if ($request->request->get('id') == 'new') {
            $role = $this->addRoleToShow($show, $request->request->get('role_type'),
                $request->request->get('role'), $request->request->get('person'));

            try {
                $this->em->flush();
                $autocomplete_person->replaceOne($role->getPerson());
            } catch (\Elastica\Exception\ExceptionInterface $ex) {
                $logger->warning('Failed to update search index during role edit',
                        ['role' => $role->getId()]);
            }

        } else {
            $id = $request->request->get('id');
            $role = $this->em->getRepository('ActsCamdramBundle:Role')
                             ->findOneById($id);
            if (!$role) {
                throw new HttpException(404, 'Role not found');
            }
            if ($role->getShow()->getId() != $show->getId()) {
                throw new HttpException(400, 'That role is not part of that show');
            }
            $role->setRole($request->request->get('role'));

            $oldPerson = $role->getPerson();
            $newPerson = $this->findOrMakePerson($request->request->get('person'));
            if ($newPerson->getId() != $oldPerson->getId()) {
                $this->removeRoleFromPerson($role, $oldPerson);
                $role->setPerson($newPerson);
                $newPerson->addRole($role);
            }

            try {
                $this->em->flush();
                $autocomplete_person->replaceOne($newPerson);
                if ($this->em->contains($oldPerson)) //person isn't deleted
                {
                    //Attempt to update role count in people search index
                    $autocomplete_person->replaceOne($oldPerson);
                }
            } catch (\Elastica\Exception\ExceptionInterface $ex) {
                $logger->warning('Failed to update search index during role edit',
                        ['role' => $role->getId()]);
            }
        }

        return new Response(json_encode([
            "id" => $role->getId(),
            "person_slug" => $role->getPerson()->getSlug()
        ]));
    }

    /**
     * Remove a role from a show. We don't need to be told the show id.
     * @Rest\Delete("/delete-role")
     */
    public function deleteRoleAction(Request $request, Helper $helper, LoggerInterface $logger, $autocomplete_person)
    {
        if (!$this->isCsrfTokenValid('delete_show_role', $request->request->get('_token'))) {
            throw new HttpException(400, 'Invalid CSRF token');
        }

        $id = $request->request->get('role');
        $role_repo = $this->em->getRepository('ActsCamdramBundle:Role');
        $role = $role_repo->findOneById($id);

        if (!$role) {
            throw new HttpException(404, "Role not found");
        }

        $show = $role->getShow();
        $helper->ensureGranted('EDIT', $show);

        $person = $role->getPerson();
        $show->removeRole($role);
        $role_repo->removeRoleFromOrder($role);
        $this->removeRoleFromPerson($role, $person);
        $this->em->remove($role);

        try {
            $this->em->flush();

            if ($this->em->contains($person)) //person isn't deleted
            {
                //Attempt to update role count in people search index
                $autocomplete_person->replaceOne($person);
            }
        }
        catch (\Elastica\Exception\ExceptionInterface $ex) {
            $logger->warning('Failed to update search index during role entry',
                    ['role' => $role->getId()]);
        }

        $response = new Response("");
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        return $response;
    }

    /**
     * Open a role-editing interface
     *
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/edit-roles")
     */
    public function getEditRolesAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->createForm(RolesType::class, array(
            array('identifier' => $identifier)));

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('show/roles-edit.html.twig');
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
    public function postManyRolesAction(Request $request, Helper $helper, LoggerInterface $logger, $autocomplete_person, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);
        $form = $this->createForm(RolesType::class, [['identifier' => $identifier]]);
        $form->handleRequest($request);

        $people = [];

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
                    /* Add a role to the show and record the person. */
                    $people[] = $this->addRoleToShow(
                        $this->getEntity($identifier),
                        $data['type'],
                        $role,
                        $name
                    )->getPerson();
                }
            }
            try {
                $this->em->flush();

                foreach ($people as $person) {
                    if ($this->em->contains($person)) {//person isn't deleted
                        //Attempt to update role count in people search index
                        $autocomplete_person->replaceOne($person);
                    }
                }
            }
            catch (\Elastica\Exception\ExceptionInterface $ex) {
                $logger->warning('Failed to update search index during bulk role entry');
            }
        } else {
            // Form not valid, hand back to user
            return $this->view($form, 400)
                ->setData(array('show' => $show, 'form' => $form->createView()))
                ->setTemplate('show/roles-new.html.twig');
        }

        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }

    /**
     * Utility function for adding a person to this show. A new person
     * record is created if they don't already exist.
     *
     * After this the calling function must call $em->flush, and
     * $autocomplete_person->replaceOne($person) on each person.
     *
     * @param Show   $show        This show.
     * @param string $role_type   The type of role ('cast', 'band', 'prod')
     * @param string $role_name   Director, Producer, Macbeth..
     * @param string $person_name The person's name
     *
     * Returns the new role.
     */
    private function addRoleToShow(Show $show, $role_type, $role_name, $person_name): Role
    {
        if (!in_array($role_type, ['cast', 'band', 'prod'])) {
            throw new HttpException(400, "Bad value of role_type");
        }
        $role = new Role();
        $role->setType($role_type);
        $role->setRole($role_name);

        $person = $this->findOrMakePerson($person_name);
        $role->setPerson($person);
        /* Append this role to the list of roles of this type. */
        $order = $this->getDoctrine()->getRepository('ActsCamdramBundle:Role')
            ->getMaxOrderByShowType($show, $role->getType());
        $role->setOrder(++$order);
        $role->setShow($show);
        $this->em->persist($role);

        $person->addRole($role);
        $show->addRole($role);

        return $role;
    }

    private function findOrMakePerson(string $person_name): Person
    {
        $person_name = trim($person_name);
        // Normalize apostrophes.
        $person_name = strtr($person_name, ['‘' => '\'', '’' => '\'']);
        /* Try and find the person. Add a new person if they don't exist. */
        $person = $this->em->getRepository('ActsCamdramBundle:Person')
                       ->findCanonicalPerson($person_name);
        if ($person == null) {
            $person = new Person();
            $person->setName($person_name);
            $this->em->persist($person);
        }
        return $person;
    }

    private function removeRoleFromPerson(Role $role, Person $person)
    {
        $person->removeRole($role);

        // Ensure the person is not an orphan.
        if ($person->getRoles()->isEmpty()) {
            $this->em->remove($person);
        }
    }
}
