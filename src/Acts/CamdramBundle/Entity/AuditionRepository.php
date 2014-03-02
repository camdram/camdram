<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Doctrine\ORM\Query\Expr;

/**
 * AuditionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AuditionRepository extends EntityRepository
{
    /**
     * CurrentOrderedByNameDate
     *
     * Find all auditions between two dates that should be shown on the
     * diary page, joined to the corresponding show.
     *
     * @return array of auditions
     */
    public function findCurrentOrderedByNameDate()
    {
        $query_res = $this->getEntityManager()->getRepository('ActsCamdramBundle:Audition');
        $now = new \DateTime();
        $query = $query_res->createQueryBuilder('a')
            ->leftJoin('a.show', 's')
            ->addSelect('s')
            ->where('a.date >= :now')
            ->andWhere('a.display = 0')
            ->andWhere('a.nonScheduled = 0')
            ->andWhere('s.authorised_by IS NOT NULL')
            ->andWhere('s.entered = true')
            ->setParameters(array('now' => $now))
            ->orderBy('s.name, a.date, a.start_time')
            ->getQuery();

        return $query->getResult();
    }

    private function getUpcomingQuery($limit)
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('a')
            ->leftJoin('a.show', 's')
            ->where('a.date >= :now')
            ->andWhere('a.nonScheduled = false')
            ->andWhere('a.show IS NOT NULL')
            ->andWhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->setParameters(array('now' => $now))
            ->orderBy('a.date')
            ->addOrderBy('a.start_time')
            ->setMaxResults($limit);
    }

    public function findUpcoming($limit)
    {
        return $this->getUpcomingQuery($limit)->getQuery()->getResult();
    }


    public function findUpcomingBySociety(Society $society, $limit)
    {
        return $this->getUpcomingQuery($limit)
            ->leftJoin('s.society', 'y')->andWhere('y = :society')->setParameter('society', $society)
            ->getQuery()->getResult();
    }

    public function findUpcomingByVenue(Venue $venue, $limit)
    {
        return $this->getUpcomingQuery($limit)
            ->leftJoin('s.venue', 'v')->andWhere('v = :venue')->setParameter('venue', $venue)
            ->getQuery()->getResult();
    }
}
