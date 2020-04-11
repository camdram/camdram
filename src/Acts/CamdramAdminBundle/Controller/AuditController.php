<?php

namespace Acts\CamdramAdminBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for viewing Camdram's log files
 *
 * @Security("has_role('ROLE_ADMIN') and is_granted('IS_AUTHENTICATED_FULLY')")
 */
class AuditController extends AbstractController
{
    private $queryParams = [
        'time_from'   => 'e.loggedAt >= ',
        'time_until'  => 'e.loggedAt <= ',
        'class'       => 'e.objectClass = ',
        'id'          => 'e.objectId = ',
        'action'      => 'e.action = ',
        'search_data' => 'e.data LIKE ',
        'search_user' => 'e.username LIKE '
    ];

    /**
     * @Route("/audit", name="acts_camdram_audit")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, EntityManagerInterface $em)
    {
        $repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $qb = $repo->createQueryBuilder('e')->orderBy('e.loggedAt', 'DESC')->setMaxResults(100);

        foreach ($request->query as $param => $value) {
            if (array_key_exists($param, $this->queryParams) && !empty($value)) {
                $qb->andWhere("{$this->queryParams[$param]} :$param")
                   ->setParameter(":$param", $value);
            }
        }

        $page = (int)($request->query->get('p', 1));
        $qb->setMaxResults(50);
        $qb->setFirstResult(50 * ($page - 1));
        $allqueries = $request->query->all();
        unset($allqueries['p']);
        $allqueries['p'] = '';

        return $this->render('admin/audit/index.html.twig', [
            'paginator' => new Paginator($qb->getQuery()),
            'page_num' => $page,
            'page_urlprefix' => explode('?', $request->getRequestUri())[0] .
                 '?' . http_build_query($allqueries),
            'queryParams' => array_keys($this->queryParams)
            ]);
    }
}
