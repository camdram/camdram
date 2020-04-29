<?php

namespace Acts\CamdramBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ShowAdminController
 *
 * Controller for site-wide show administration.
 *
 * @RouteResource("My-Show")
 * @IsGranted("ROLE_USER")
 */
class ShowAdminController extends AbstractController
{
    /**
     * Lists all the user's shows.
     */
    public function cgetAction(Request $request, AclProvider $aclProvider, EntityManagerInterface $entityManager)
    {
        $ids = $aclProvider->getEntitiesByUser($this->getUser(), '\\Acts\\CamdramBundle\\Entity\\Show');
        $shows = $entityManager->getRepository('ActsCamdramBundle:Show')->findIdsByDate($ids);

        return $this->render('show_admin/index.html.twig', array('shows' => $shows));
    }

    /**
     * Lists all the shows waiting for approval by this user.
     */
    public function cgetUnauthorisedAction(Request $request, ModerationManager $moderationManager)
    {
        $shows = $moderationManager->getEntitiesToModerate();

        return $this->render('show_admin/unauthorised.html.twig', array('shows' => $shows));
    }
}
