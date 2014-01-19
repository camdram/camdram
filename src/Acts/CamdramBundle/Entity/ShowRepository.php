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
       // var_dump($query->getSQL());die();
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

}