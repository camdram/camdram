<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Doctrine\ORM\Query\Expr;

/**
 * ApplicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApplicationRepository extends EntityRepository
{    
    /**
     * findScheduledOrderedByDeadline
     *
     * Find all applications between two dates that should be shown on the
     * diary page.
     *
     * @param integer $startDate start date expressed as a Unix timestamp
     * @param integer $endDate emd date expressed as a Unix timestamp
     *
     * @return array of applications
     */
    public function findScheduledOrderedByDeadline($startDate, $endDate)
    {
        $query_res = $this->getEntityManager()->getRepository('ActsCamdramBundle:Application');
        $query = $query_res->createQueryBuilder('a')
            ->where('a.deadlineDate <= :enddate')
            ->andWhere('a.deadlineDate >= :startdate')
            ->andWhere('a.deadlineDate >= CURRENT_DATE()')
            ->setParameters(array(
                'startdate' => date("Y/m/d", $startDate),
                'enddate' => date("Y/m/d", $endDate)
                ))
            ->orderBy('a.deadlineDate')
            ->getQuery();

        return $query->getResult();
    }

    private function getLatestQuery($limit)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.show', 's')
            ->where('a.deadlineDate >= CURRENT_DATE()')
            ->andWhere('s.authorised_by is not null')
            ->andWhere('s.entered != false')
            ->orderBy('a.deadlineDate', 'DESC')
            ->setMaxResults($limit);
    }

    public function findLatest($limit)
    {
        return $this->getLatestQuery($limit)->getQuery()->getResult();
    }

    public function findLatestBySociety(Society $society, $limit)
    {
        $qb = $this->getLatestQuery($limit);
        $qb->leftJoin('s.society', 'y')->andWhere(
                    $qb->expr()->orX('y = :society', 'a.society = :society')
            )->setParameter('society', $society)
            ->getQuery()->getResult();
    }

    public function findLatestByVenue(Venue $venue, $limit)
    {
        $qb = $this->getLatestQuery($limit);
        $qb->leftJoin('s.venue', 'v')->andWhere('v = :venue')->setParameter('venue', $venue)
            ->getQuery()->getResult();
    }
}
