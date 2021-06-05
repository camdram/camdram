<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends AbstractController
{
    /**
     * reorderRolesAction
     *
     * Reorder the display ordering for the array of Roles identified by their ID.
     *
     * @Route("/roles/reorder", methods={"PATCH"}, name="patch_roles_reorder")
     */
    public function patchRolesReorderAction(Request $request, Helper $_helper): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Role::class);
        // The following line will throw if the user passes a scalar, e.g. ?role=x.
        $role_ids = $request->request->all('role');
        $failed = [];
        $i = 0;
        foreach ($role_ids as $id) {
            $role = $repo->findOneById($id);
            $can_reorder = $role !== null && $_helper->isGranted('EDIT', $role->getShow());

            if (!$can_reorder) {
                $failed[] = $id;
            } else {
                if ($role->getOrder() != $i) $role->setOrder($i);
                ++$i;
            }
        }
        $em->flush();
        $em->clear();
        return new JsonResponse(array_filter([
            'failures' => $failed
        ]));
    }

    /**
     * Allow the person named in a role to set a tag on it
     * Required params:
     *    role,   int (role id)
     *    newtag, string. (If blank, interpret as null)
     *    _token  CSRF token
     * @Route("/roles/settag", methods={"PATCH"}, name="patch_roles_settag")
     */
    public function patchRolesSetTagAction(Request $request, Helper $_helper): Response
    {
        if (!$this->isCsrfTokenValid('patch_roles_settag', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em     = $this->getDoctrine()->getManager();
        $repo   = $em->getRepository(Role::class);
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
