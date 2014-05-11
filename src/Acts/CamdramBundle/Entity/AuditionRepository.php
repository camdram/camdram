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
    public function findCurrentOrderedByNameDate(\DateTime $now)
    {
        $query_res = $this->getEntityManager()->getRepository('ActsCamdramBundle:Audition');
        $qb = $query_res->createQueryBuilder('a');
        $qb->leftJoin('ActsCamdramBundle:Show', 's', Expr\Join::WITH, 'a.show = s.id')
            ->where($qb->expr()->orX('a.date > :current_date', $qb->expr()->andX('a.date = :current_date', 'a.end_time >= :current_time')))
            ->andWhere('a.display = 0')
            ->andWhere('s.authorised_by IS NOT NULL')
            ->andWhere('s.entered = true')
            ->orderBy('s.name, a.date, a.start_time, a.nonScheduled')
            ->setParameter('current_date', $now, \Doctrine\DBAL\Types\Type::DATE)
            ->setParameter('current_time', $now, \Doctrine\DBAL\Types\Type::TIME)
            ->getQuery();

        return $qb->getQuery()->getResult();
    }

    private function getUpcomingQuery($limit, \DateTime $now)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->leftJoin('a.show', 's')
            ->where($qb->expr()->orX('a.date > :current_date', $qb->expr()->andX('a.date = :current_date', 'a.end_time >= :current_time')))
            ->andWhere('a.nonScheduled = false')
            ->andWhere('a.show IS NOT NULL')
            ->andWhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->orderBy('a.date')
            ->addOrderBy('a.start_time')
            ->setParameter('current_date', $now, \Doctrine\DBAL\Types\Type::DATE)
            ->setParameter('current_time', $now, \Doctrine\DBAL\Types\Type::TIME)
            ->setMaxResults($limit);

        return $qb;
    }
    
    public function getUpcomingNonScheduledQuery($limit, \DateTime $now)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->leftJoin('a.show', 's')
            ->where($qb->expr()->orX('a.date > :current_date', $qb->expr()->andX('a.date = :current_date', 'a.end_time >= :current_time')))
            ->andWhere('a.nonScheduled = true')
            ->andWhere('a.show IS NOT NULL')
            ->andWhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->orderBy('a.date')
            ->addOrderBy('a.start_time')
            ->setParameter('current_date', $now, \Doctrine\DBAL\Types\Type::DATE)
            ->setParameter('current_time', $now, \Doctrine\DBAL\Types\Type::TIME)
            ->setMaxResults($limit);
        return $qb;
    }

    public function findUpcoming($limit, \DateTime $now)
    {
        return $this->getUpcomingQuery($limit, $now)->getQuery()->getResult();
    }

    public function findUpcomingNonScheduled($limit, \DateTime $now)
    {
        return $this->getUpcomingNonScheduledQuery($limit, $now)->getQuery()->getResult();
    }

    public function findUpcomingBySociety(Society $society, $limit, \DateTime $now)
    {
        return $this->getUpcomingQuery($limit, $now)
            ->leftJoin('s.society', 'y')->andWhere('y = :society')->setParameter('society', $society)
            ->getQuery()->getResult();
    }

    public function findUpcomingNonScheduledBySociety(Society $society, $limit, \DateTime $now)
    {
        return $this->getUpcomingNonScheduledQuery($limit, $now)
            ->leftJoin('s.society', 'y')->andWhere('y = :society')->setParameter('society', $society)
            ->getQuery()->getResult();
    }

    public function findUpcomingByVenue(Venue $venue, $limit, \DateTime $now)
    {
        return $this->getUpcomingQuery($limit, $now)
            ->leftJoin('s.venue', 'v')->andWhere('v = :venue')->setParameter('venue', $venue)
            ->getQuery()->getResult();
    }

    public function findUpcomingNonScheduledByVenue(Venue $venue, $limit, \DateTime $now)
    {
        return $this->getUpcomingNonScheduledQuery($limit, $now)
            ->leftJoin('s.venue', 'v')->andWhere('v = :venue')->setParameter('venue', $venue)
            ->getQuery()->getResult();
    }
}
