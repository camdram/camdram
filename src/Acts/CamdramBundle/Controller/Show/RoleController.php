<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\RolesType;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function getEntity($identifier)
    {
        return $this->em->getRepository(Show::class)->findOneBy(array('slug' => $identifier));
    }

    /**
     * Add or modify a role and return relevant data for the form.
     *
     * Creates a new person if they're not already part of Camdram.
     *
     * @Route("/shows/{identifier}/roles", methods={"PATCH"}, name="patch_show_role")
     */
    public function patchRoleAction(Request $request, Helper $helper, LoggerInterface $logger, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        if (!$this->isCsrfTokenValid('patch_show_role', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $person = $request->request->get('person');
        $role_text = $request->request->get('role');
        $role_type = $request->request->get('role_type');
        if (!($person && $role_text)) {
            throw new BadRequestHttpException('Missing required fields');
        }

        if ($request->request->get('id') == 'new') {
            $role = $this->addRoleToShow($show, $role_type, $role_text, $person);

            $this->em->flush();
        } else {
            $id = $request->request->get('id');
            $role = $this->em->getRepository(Role::class)
                             ->findOneById($id);
            if (!$role) {
                throw new NotFoundHttpException('Role not found');
            }
            if ($role->getShow()->getId() != $show->getId()) {
                throw new BadRequestHttpException('That role is not part of that show');
            }
            $role->setRole($role_text);

            $oldPerson = $role->getPerson();
            $newPerson = $this->findOrMakePerson($person);
            if ($newPerson->getId() != $oldPerson->getId()) {
                $this->removeRoleFromPerson($role, $oldPerson);
                $role->setPerson($newPerson);
                $newPerson->addRole($role);
            }

            $this->em->flush();
        }

        return new Response(json_encode([
            "id" => $role->getId(),
            "person_slug" => $role->getPerson()->getSlug()
        ]));
    }

    /**
     * Remove a role from a show. We don't need to be told the show id.
     * @Route("/delete-role", methods={"DELETE"}, name="delete_show_role")
     */
    public function deleteRoleAction(Request $request, Helper $helper, LoggerInterface $logger)
    {
        if (!$this->isCsrfTokenValid('delete_show_role', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $id = $request->request->get('role');
        $role_repo = $this->em->getRepository(Role::class);
        $role = $role_repo->findOneById($id);

        if (!$role) {
            throw new NotFoundHttpException("Role not found");
        }

        $show = $role->getShow();
        $helper->ensureGranted('EDIT', $show);

        $person = $role->getPerson();
        $show->removeRole($role);
        $role_repo->removeRoleFromOrder($role);
        $this->removeRoleFromPerson($role, $person);
        $this->em->remove($role);
        $this->em->flush();

        $response = new Response("");
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        return $response;
    }

    /**
     * Open a role-editing interface
     *
     * @Route("/shows/{identifier}/edit-roles", methods={"GET"}, name="get_show_edit_roles")
     */
    public function getEditRolesAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->createForm(RolesType::class, array(
            array('identifier' => $identifier)));

        return $this->render('show/roles-edit.html.twig',
            ['show' => $show, 'form' => $form->createView()]);
    }

    /**
     * Process adding multiple roles to a show.
     *
     * This function doesn't do validation of the data, e.g. ensuring
     * that the role and the person aren't reversed, as this is the
     * responsibility of the form validation. This function should be robust
     * against badly formatted input-however.
     *
     * @Route("/shows/{identifier}/many-roles", methods={"POST"}, name="post_show_many_roles")
     */
    public function postManyRolesAction(Request $request, Helper $helper, LoggerInterface $logger, $identifier)
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
            $this->em->flush();
        } else {
            // Form not valid, hand back to user
            return $this->render('show/roles-new.html.twig', ['show' => $show, 'form' => $form->createView()])
                ->setStatusCode(400);
        }

        return $this->redirectToRoute('get_show', array('identifier' => $show->getSlug()));
    }

    /**
     * Utility function for adding a person to this show. A new person
     * record is created if they don't already exist.
     *
     * After this the calling function must call $em->flush.
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
            throw new BadRequestHttpException("Bad value of role_type");
        }
        $role = new Role();
        $role->setType($role_type);
        $role->setRole($role_name);

        $person = $this->findOrMakePerson($person_name);
        $role->setPerson($person);
        /* Append this role to the list of roles of this type. */
        $order = $this->getDoctrine()->getRepository(Role::class)
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
        $person = $this->em->getRepository('Acts\\CamdramBundle\\Entity\\Person')
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
