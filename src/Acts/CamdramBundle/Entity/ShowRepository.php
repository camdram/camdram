<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr as Expr;

/**
 * ShowRepository
 *
 */
class ShowRepository extends EntityRepository
{

    public function selectAll()
    {
        //Need to add a 'created_at' field...
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->orderBy('s.id', 'desc');
        return $qb;
    }

    public function findUnauthorised()
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised_by is null')
            ->andWhere('s.entered = true')
            ->join('s.performances', 'p');
        return $qb->getQuery()->getResult();
    }

    public function findUnauthorisedBySociety(Society $society)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised_by is null')
            ->andWhere('s.entered = true')
            ->andWhere('s.society = :society')
            ->setParameter('society', $society)
            ->join('s.performances', 'p');
        return $qb->getQuery()->getResult();
    }

    public function findUnauthorisedByVenue(Venue $venue)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised_by is null')
            ->andWhere('s.entered = true')
            ->andWhere('s.venue = :venue')
            ->setParameter('venue', $venue)
            ->join('s.performances', 'p');
        return $qb->getQuery()->getResult();
    }

    public function findIdsByDate($ids)
    {
        if (count($ids) == 0) return array();

        $qb = $this->createQueryBuilder('s')
            ->where('s.id IN (:ids)')
            ->andWhere('s.entered = true')
            ->orderBy('s.start_at', 'desc')
            ->setParameter('ids', $ids);
        return $qb->getQuery()->getResult();
    }

    public function getNumberInDateRange(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('s')->select('COUNT(DISTINCT s.id)')
            ->where('s.authorised_by is not null')
            ->andWhere('s.entered = true');

        $qb->innerJoin('ActsCamdramBundle:Performance', 'p',Expr\Join::WITH, $qb->expr()->andX(
                'p.show = s',
                $qb->expr()->andX('p.start_date <= :end', 'p.end_date >= :start')
            ))
            ->setParameter('start', $start)
            ->setParameter('end', $end);
        $result = $qb->getQuery()->getOneOrNullResult();
        return current($result);
    }

    public function getNumberOfVenueNamesInDateRange(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('s')->select('COUNT(DISTINCT s.venue_name)')
            ->where('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->andwhere('s.venue IS NULL');

        $qb->innerJoin('ActsCamdramBundle:Performance', 'p',Expr\Join::WITH, $qb->expr()->andX(
            'p.show = s',
            $qb->expr()->andX('p.start_date <= :end', 'p.end_date >= :start')
        ))
            ->setParameter('start', $start)
            ->setParameter('end', $end);
        $result = $qb->getQuery()->getOneOrNullResult();
        return current($result);
    }

    public function findInDateRange(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->join('s.performances', 'p')
            ->andWhere('p.start_date <= :end')
            ->andWhere('p.end_date >= :start')
            ->setParameter('start', $start)
            ->setParameter('end', $end);
        return $qb->getQuery()->getResult();
    }

    public function findMostInterestingByTimePeriod(TimePeriod $period, $limit)
    {
        //For now, we define 'most interesting' as 'lasts the longest period of time'
        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.performances', 'p')
            ->where('p.start_date < :end')
            ->andWhere('p.end_date > :start')
            ->andWhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->setParameter('start', $period->getStartAt())
            ->setParameter('end', $period->getEndAt())
            ->orderBy('s.end_at - s.start_at', 'DESC')
            ->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }

    public function getUpcomingByPerson(\DateTime $now, Person $person)
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.start_at >= :now')
            ->andWhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->join('ActsCamdramBundle:Role', 'r')
            ->andWhere('r.person = :person')
            ->orderBy('s.start_at', 'ASC')
            ->setParameter('person', $person)
            ->setParameter('now', $now)
            ->getQuery();
        return $query->getResult();
    }

    public function getCurrentByPerson(\DateTime $now, Person $person)
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.end_at >= :now')
            ->andWhere('s.start_at < :now')
            ->andWhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->join('ActsCamdramBundle:Role', 'r')
            ->andWhere('r.person = :person')
            ->orderBy('s.start_at', 'ASC')
            ->setParameter('person', $person)
            ->setParameter('now', $now)
            ->getQuery();
        return $query->getResult();
    }

    public function getPastByPerson(\DateTime $now, Person $person)
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.end_at < :now')
            ->andWhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->join('ActsCamdramBundle:Role', 'r')
            ->andWhere('r.person = :person')
            ->orderBy('s.start_at', 'DESC')
            ->setParameter('person', $person)
            ->setParameter('now', $now)
            ->getQuery();
        return $query->getResult();
    }

    public function getFirstShowDate()
    {
        $query = $this->createQueryBuilder('s')
            ->select('MIN(s.start_at)')
            ->where('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->andWhere('s.start_at IS NOT NULL')
            ->andWhere('s.start_at > :min')
            ->setMaxResults(1)
            ->setParameter('min', new \DateTime('1990-01-01'))
            ->getQuery();
        return new \DateTime(current($query->getOneOrNullResult()));
    }

    public function getLastShowDate()
    {
        $query = $this->createQueryBuilder('s')
            ->select('MAX(s.end_at)')
            ->where('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->setMaxResults(1)
            ->getQuery();
        return new \DateTime(current($query->getOneOrNullResult()));
    }
    
    /**
     *  Takes a list of shows and loads all associated performances in one database hit.
     */
    public function GetShowsWithAllPerformances($showIdsArray)    
    {
        $query = $this->createQueryBuilder('s')
                    ->leftJoin('s.performances','p')
                    ->addSelect('p')
                    ->where('s.id in (:ids)')
                    ->setParameter('ids',$showIdsArray)
                    ->getQuery();
        return $query->getResult();
    }

}
