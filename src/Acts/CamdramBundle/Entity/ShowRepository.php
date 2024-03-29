<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * ShowRepository
 * @extends EntityRepository<Show>
 */
class ShowRepository extends EntityRepository
{
    public function selectAll()
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.authorised = true')
            ->leftJoin('s.performances', 'p')
            ->orderBy('p.start_at', 'desc')
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
            ->andWhere('EXISTS (SELECT pp FROM \Acts\CamdramBundle\Entity\Performance pp WHERE pp.show = s AND pp.venue = :venue)')
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
            ->join('s.performances', 'p')
            ->orderBy('p.start_at', 'desc')
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    public function getNumberInDateRange(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('s')->select('COUNT(DISTINCT s.id)')
            ->innerJoin('s.performances', 'p')
            ->where('s.authorised = true')
            ->andWhere('p.start_at < :end')
            ->andWhere('p.repeat_until >= :start')
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
            ->andWhere('p.start_at <= :end')
            ->andWhere('p.repeat_until >= :start')
            ->orderBy('p.start_at')
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
            ->where('p.start_at < :end')
            ->andWhere('p.repeat_until > :start')
            ->andWhere('s.authorised = true')
            ->setParameter('start', $week->getStartAt())
            ->setParameter('end', $week->getEndAt())
            ->orderBy('p.repeat_until - p.start_at', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function getByPerson(Person $person)
    {
        $query = $this->createQueryBuilder('s')
            ->leftJoin('s.performances', 'p')
            ->join('s.roles', 'r')
            ->andwhere('s.authorised = true')
            ->andWhere('r.person = :person')
            ->orderBy('p.start_at')
            ->groupBy('s.id')
            ->setParameter('person', $person)
            ->getQuery();

        return $query->getResult();
    }

    private function queryByPerson(\DateTime $now, Person $person)
    {
        return $this->createQueryBuilder('s')
            ->select('s, p')
            ->join('s.roles', 'r')
            ->leftJoin('s.performances', 'p')
            ->where('s.authorised = true')
            ->andWhere('r.person = :person')
            ->groupBy('s.id')
            ->setParameter('now', $now)
            ->setParameter('person', $person);
    }

    public function getUpcomingByPerson(\DateTime $now, Person $person)
    {
        return $this->queryByPerson($now, $person)
            ->orderBy('p.start_at', 'ASC')
            ->having('DATE_DIFF(MIN(p.start_at), :now) > 0')
            ->getQuery()
            ->getResult();
    }

    public function getCurrentByPerson(\DateTime $now, Person $person)
    {
        return $this->queryByPerson($now, $person)
            ->orderBy('p.start_at', 'ASC')
            ->having('DATE_DIFF(MIN(p.start_at), :now) <= 0')
            ->andHaving('DATE_DIFF(MAX(p.repeat_until), :now) >= 0')
            ->getQuery()
            ->getResult();
    }

    public function getPastByPerson(\DateTime $now, Person $person)
    {
        return $this->queryByPerson($now, $person)
            ->orderBy('p.repeat_until', 'DESC')
            ->having('DATE_DIFF(MAX(p.repeat_until), :now) < 0')
            ->getQuery()
            ->getResult();
    }

    public function queryByOrganisation(Organisation $org, \DateTime $from = null, \DateTime $to = null): QueryBuilder
    {
        if ($org instanceof Society) {
            return $this->queryBySociety($org, $from, $to);
        } else if ($org instanceof Venue) {
            return $this->queryByVenue($org, $from, $to);
        } else throw new \LogicException();
    }

    private function queryBySociety(Society $society, \DateTime $from = null, \DateTime $to = null): QueryBuilder
    {
        $query = $this->createQueryBuilder('s')
            ->join('s.performances', 'p')
            ->addSelect('s')
            ->where('s.authorised = true')
            ->andWhere(':society MEMBER OF s.societies');

        if ($from) {
            $query = $query->andWhere('p.start_at > :from')->setParameter('from', $from);
        }

        if ($to) {
            $query = $query->andWhere('p.repeat_until <= :to')->setParameter('to', $to);
        }

        return $query->orderBy('p.start_at', 'ASC')
            ->setParameter('society', $society)
            ->groupBy('s.id');
    }

    public function getBySociety(Society $society, \DateTime $from = null, \DateTime $to = null)
    {
        return $this->queryBySociety($society, $from, $to)->getQuery()->getResult();
    }

    private function queryByVenue(Venue $venue, \DateTime $from = null, \DateTime $to = null): QueryBuilder
    {
        $query = $this->createQueryBuilder('s')
            ->join('s.performances', 'p')
            ->addSelect('s')
            ->where('s.authorised = true');

        if ($from) {
            $query = $query->andWhere('p.start_at > :from')->setParameter('from', $from);
        }

        if ($to) {
            $query = $query->andWhere('p.repeat_until <= :to')->setParameter('to', $to);
        }

        return $query->andWhere('p.venue = :venue')
            ->orderBy('p.start_at', 'ASC')
            ->setParameter('venue', $venue)
            ->groupBy('s.id');
    }

    public function getByVenue(Venue $venue, \DateTime $from = null, \DateTime $to = null)
    {
        return $this->queryByVenue($venue, $from, $to)->getQuery()->getResult();
    }
}
