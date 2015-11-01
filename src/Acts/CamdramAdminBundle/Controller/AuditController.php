<?php

namespace Acts\CamdramAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for viewing Camdram's log files
 */
class AuditController extends Controller
{
    public function indexAction(Request $request)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_ADMIN');

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

        return $this->render('ActsCamdramAdminBundle:Audit:index.html.twig', array('results' => $results));
    }
}
