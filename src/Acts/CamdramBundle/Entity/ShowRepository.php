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
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->leftJoin('s.performances', 'p')
            ->orderBy('p.end_date', 'desc')
            ->addOrderBy('p.start_date', 'desc')
            ->groupBy('s.id');
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
            ->innerJoin('s.performances', 'p')
            ->where('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->andWhere('p.start_date < :end')
            ->andWhere('p.end_date >= :start')
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
            ->orderBy('p.end_date')
            ->addOrderBy('p.start_date')
            ->groupBy('s.id')
            ->setParameter('start', $start)
            ->setParameter('end', $end);
        return $qb->getQuery()->getResult();
    }

    public function findMostInterestingByWeek(Week $week, $limit)
    {
        //For now, we define 'most interesting' as 'lasts the longest period of time'
        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.performances', 'p')
            ->where('p.start_date < :end')
            ->andWhere('p.end_date > :start')
            ->andWhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->setParameter('start', $week->getStartAt())
            ->setParameter('end', $week->getEndAt())
            ->orderBy('p.end_date - p.start_date', 'DESC')
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

    public function getBySociety(Society $society, \DateTime $from = null, \DateTime $to = null)
    {
        $query = $this->createQueryBuilder('s')
            ->join('s.performances', 'p')
            ->leftjoin('s.venue', 'v')
            ->addSelect('s')
            ->addSelect('v')
            ->where('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->andWhere('s.society = :society');

        if($from){
            $query = $query->andWhere('p.start_date > :from')->setParameter('from', $from);
        }

        if($to){
            $query = $query->andWhere('p.end_date <= :to')->setParameter('to', $to);
        }

        $query = $query->orderBy('p.start_date', 'ASC')
            ->setParameter('society', $society)
            ->groupBy('s.id')
            ->getQuery();
        return $query->getResult();
    }

    public function getByVenue(Venue $venue, \DateTime $from = null, \DateTime $to = null)
    {
        $query = $this->createQueryBuilder('s')
            ->join('s.performances', 'p')
            ->join('s.venue', 'v')
            ->addSelect('s')
            ->addSelect('v')
            ->where('s.authorised_by is not null')
            ->andWhere('s.entered = true');

        if($from){
            $query = $query->andWhere('p.start_date > :from')->setParameter('from', $from);
        }

        if($to){
            $query = $query->andWhere('p.end_date <= :to')->setParameter('to', $to);
        }

        $query = $query->andWhere('s.venue = :venue')
            ->orderBy('p.start_date', 'ASC')
            ->setParameter('venue', $venue)
            ->groupBy('s.id')
            ->getQuery();
        return $query->getResult();
    }

}
