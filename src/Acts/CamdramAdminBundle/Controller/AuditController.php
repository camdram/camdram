<?php

namespace Acts\CamdramAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for viewing Camdram's log files
 *
 * @Security("has_role('ROLE_ADMIN') and is_granted('IS_AUTHENTICATED_FULLY')")
 */
class AuditController extends Controller
{
    /**
     * @Route("/audit", name="acts_camdram_audit")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $repo = $this->get('doctrine.orm.entity_manager')->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $qb = $repo->createQueryBuilder('e')->orderBy('e.loggedAt', 'DESC')->setMaxResults(100);
        if ($request->query->has('class')) {
            $qb->andWhere('e.objectClass = :class')
                ->setParameter('class', $request->query->get('class'));
        }
        if ($request->query->has('id')) {
            $qb->andWhere('e.objectId = :id')
                ->setParameter('id', $request->query->get('id'));
        }

        $results = $qb->getQuery()->getResult();

        return $this->render('admin/audit/index.html.twig', array('results' => $results));
    }
}
