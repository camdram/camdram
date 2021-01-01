<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowAdminController
 *
 * Controller for site-wide show administration.
 *
 * @IsGranted("ROLE_USER")
 */
class ShowAdminController extends AbstractController
{
    /**
     * Lists all the user's shows and shows awaiting approval.
     * @Route("/show-admin", methods={"GET"}, name="acts_camdram_show_admin")
     */
    public function cgetAction(Request $request, AclProvider $aclProvider, EntityManagerInterface $entityManager, ModerationManager $moderationManager): Response
    {
        $ids = $aclProvider->getEntitiesByUser($this->getUser(), '\\Acts\\CamdramBundle\\Entity\\Show');
        $shows = $entityManager->getRepository(Show::class)->findIdsByDate($ids);
        $unauthorised = $moderationManager->getEntitiesToModerate();

        return $this->render('show_admin/index.html.twig', compact('shows', 'unauthorised'));
    }
}
