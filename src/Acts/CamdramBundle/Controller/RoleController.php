<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 */
class RoleController extends AbstractFOSRestController
{
    /**
     * reorderRolesAction
     *
     * Reorder the display ordering for the array of Roles identified by their ID.
     *
     * @Rest\Patch("/roles/reorder", name="patch_roles_reorder")
     */
    public function patchRolesReorderAction(Request $request, Helper $_helper): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ActsCamdramBundle:Role');
        $role_ids = $request->request->get('role');
        $i = 0;
        foreach ($role_ids as $id) {
            $role = $repo->findOneById($id);
            $can_reorder = $_helper->isGranted('EDIT', $role->getShow());
            if (($role->getOrder() != $i) && $can_reorder) {
                $role->setOrder($i);
            }
            ++$i;
        }
        $em->flush();
        $em->clear();
        $response = new Response();
        $response->setStatusCode(204); // Success no content
        return $response;
    }

    /**
     * Allow the person named in a role to set a tag on it
     * Required params:
     *    role,   int (role id)
     *    newtag, string. (If blank, interpret as null)
     * @Rest\Patch("/roles/settag", name="patch_roles_settag")
     */
    public function patchRolesSetTagAction(Request $request, Helper $_helper): Response
    {
        $em     = $this->getDoctrine()->getManager();
        $repo   = $em->getRepository('ActsCamdramBundle:Role');
        $id     = $request->request->get('role');
        $newtag = trim($request->request->get('newtag'));
        $role   = $repo->findOneById($id);

        if (!$role) return new Response('role not found', 404);

        $_helper->ensureGranted('EDIT', $role->getPerson());
        $role->setTag(empty($newtag) ? NULL : $newtag);
        $em->flush();
        $em->clear();
        return new Response('', 204); // Success no content
    }
}
