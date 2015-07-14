<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Acts\CamdramBundle\Entity\Role;

/**
 */
class RoleController extends FOSRestController
{
    /**
     * reorderRolesAction
     *
     * Reorder the display ordering for the array of Roles identified by their ID.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patchRolesReorderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ActsCamdramBundle:Role');
        $role_ids = $request->request->get('role');
        $i = 0;
        foreach ($role_ids as $id) {
            $role = $repo->findOneById($id);
            $can_reorder = $this->get('camdram.security.acl.helper')->isGranted('EDIT', $role->getShow());
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
}
