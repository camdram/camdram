<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ShowRepository
 */
class ShowRepository extends EntityRepository
{
    public function selectAll()
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised = true')
            ->leftJoin('s.performances', 'p')
            ->orderBy('p.end_date', 'desc')
            ->addOrderBy('p.start_date', 'desc')
            ->groupBy('s.id');

        return $qb;
    }

    public function findUnauthorised()
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised = false')
            ->join('s.performances', 'p');

        return $qb->getQuery()->getResult();
    }

    public function findUnauthorisedBySociety(Society $society)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised = false')
            ->andWhere(':society MEMBER OF s.societies')
            ->setParameter('society', $society)
            ->join('s.performances', 'p');

        return $qb->getQuery()->getResult();
    }

    public function findUnauthorisedByVenue(Venue $venue)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised = false')
            ->andWhere('s.venue = :venue')
            ->setParameter('venue', $venue)
            ->join('s.performances', 'p');

        return $qb->getQuery()->getResult();
    }

    public function findIdsByDate($ids)
    {
        if (count($ids) == 0) {
            return array();
        }

        $qb = $this->createQueryBuilder('s')
            ->where('s.id IN (:ids)')
            ->orderBy('s.start_at', 'desc')
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    public function getNumberInDateRange(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('s')->select('COUNT(DISTINCT s.id)')
            ->innerJoin('s.performances', 'p')
            ->where('s.authorised = true')
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
            ->where('s.authorised = true')
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
            ->andWhere('s.authorised = true')
            ->setParameter('start', $week->getStartAt())
            ->setParameter('end', $week->getEndAt())
            ->orderBy('p.end_date - p.start_date', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function getUpcomingByPerson(\DateTime $now, Person $person)
    {
        $query = $this->createQueryBuilder('s')
            ->join('s.roles', 'r')
            ->leftJoin('s.performances', 'p')
            ->where('s.authorised = true')
            ->andWhere('r.person = :person')
            ->orderBy('p.start_date', 'ASC')
            ->groupBy('s.id')
            ->having('MIN(p.start_date) >= :now')
            ->setParameter('person', $person)
            ->setParameter('now', $now)
            ->getQuery();

        return $query->getResult();
    }

    public function getCurrentByPerson(\DateTime $now, Person $person)
    {
        $query = $this->createQueryBuilder('s')
            ->join('s.roles', 'r')
            ->leftJoin('s.performances', 'p')
            ->where('s.authorised = true')
            ->andWhere('r.person = :person')
            ->orderBy('p.start_date', 'ASC')
            ->groupBy('s.id')
            ->having('MIN(p.start_date) < :now')
            ->andHaving('MAX(p.end_date) >= :now')
            ->setParameter('person', $person)
            ->setParameter('now', $now)
            ->getQuery();

        return $query->getResult();
    }

    public function getPastByPerson(\DateTime $now, Person $person)
    {
        $query = $this->createQueryBuilder('s')
            ->leftJoin('s.performances', 'p')
            ->join('s.roles', 'r')
            ->andwhere('s.authorised = true')
            ->andWhere('r.person = :person')
            ->orderBy('p.end_date', 'DESC')
            ->groupBy('s.id')
            ->having('MAX(p.end_date) < :now')
            ->setParameter('person', $person)
            ->setParameter('now', $now)
            ->getQuery();

        return $query->getResult();
    }

    public function getFirstShowDate()
    {
        $query = $this->createQueryBuilder('s')
            ->select('MIN(s.start_at)')
            ->where('s.authorised = true')
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
            ->where('s.authorised = true')
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
            ->where('s.authorised = true')
            ->andWhere(':society MEMBER OF s.societies');

        if ($from) {
            $query = $query->andWhere('p.start_date > :from')->setParameter('from', $from);
        }

        if ($to) {
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
            ->where('s.authorised = true');

        if ($from) {
            $query = $query->andWhere('p.start_date > :from')->setParameter('from', $from);
        }

        if ($to) {
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
