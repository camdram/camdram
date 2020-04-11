<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShowAdminController
 *
 * Controller for site-wide show administration.
 *
 * @RouteResource("My-Show")
 */
class ShowAdminController extends AbstractController
{
    /**
     * Lists all the user's shows.
     */
    public function cgetAction(Request $request)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_USER');

        $ids = $this->get('camdram.security.acl.provider')->getEntitiesByUser($this->getUser(), '\\Acts\\CamdramBundle\\Entity\\Show');
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findIdsByDate($ids);

        return $this->render('show_admin/index.html.twig', array('shows' => $shows));
    }

    /**
     * Lists all the shows waiting for approval by this user.
     */
    public function cgetUnauthorisedAction(Request $request)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_USER');
        $shows = $this->get('acts.camdram.moderation_manager')->getEntitiesToModerate();

        return $this->render('show_admin/unauthorised.html.twig', array('shows' => $shows));
    }
}
