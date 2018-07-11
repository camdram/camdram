<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShowController
 *
 * Controller for REST actions for shows. Inherits from AbstractRestController.
 *
 * @RouteResource("My-Show")
 */
class ShowAdminController extends Controller
{
    public function cgetAction(Request $request)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_USER');

        $ids = $this->get('camdram.security.acl.provider')->getEntitiesByUser($this->getUser(), '\\Acts\\CamdramBundle\\Entity\\Show');
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findIdsByDate($ids);

        return $this->render('show_admin/index.html.twig', array('shows' => $shows));
    }

    public function cgetUnauthorisedAction(Request $request)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_USER');
        $shows = $this->get('acts.camdram.moderation_manager')->getEntitiesToModerate();

        return $this->render('show_admin/unauthorised.html.twig', array('shows' => $shows));
    }
}
