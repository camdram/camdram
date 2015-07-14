<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TimePeriodRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TimePeriodRepository extends EntityRepository
{
    public function findAt(\DateTime $date)
    {
        $qb = $this->createQueryBuilder('p');
        $query = $qb->where($qb->expr()->andX('p.start_at <= :now', 'p.end_at > :now'))
            ->setParameter('now', $date)
            ->orderBy('p.start_at', 'ASC')
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findBetween($start_date, $end_date, $limit = null)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.start_at >= :start_date')
            ->andWhere('p.end_at < :end_date')
            ->setParameter('start_date', $start_date)
            ->setParameter('end_date', $end_date)
            ->orderBy('p.start_at', 'ASC');
        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findStartsIn(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('p');

        $query = $qb
            ->where($qb->expr()->andX('p.start_at < :end', 'p.start_at >= :start'))
            ->setParameter('start', $start)->setParameter('end', $end)
            ->addOrderBy('p.start_at', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function findIntersecting(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('p');

        $query = $qb
            ->where($qb->expr()->andX('p.start_at <= :start', 'p.end_at > :start'))
            ->orWhere($qb->expr()->andX('p.start_at <= :end', 'p.end_at > :end'))
            ->orWhere($qb->expr()->andX('p.start_at >= :start', 'p.end_at < :end'))
            ->setParameter('start', $start)->setParameter('end', $end)
            ->addOrderBy('p.start_at', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function findByYear($year)
    {
        $year = (int) $year;
        $start = new \DateTime($year.'-01-01');
        $end = new \DateTime(($year + 1).'-01-01');

        return $this->findStartsIn($start, $end);
    }

    public function findByYearBefore($year, \DateTime $before)
    {
        $year = (int) $year;
        $start = new \DateTime($year.'-01-01');
        $end = new \DateTime(($year + 1).'-01-01');

        if ($before < $end) {
            $end = $before;
        }

        return $this->findStartsIn($start, $end);
    }

    public function getBySlugAndYear($slug, $year)
    {
        $year = (int) $year;
        $start = new \DateTime($year.'-01-01');
        $end = new \DateTime(($year + 1).'-01-01');

        $query = $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->andWhere('p.start_at >= :start_date')
            ->andWhere('p.start_at < :end_date')
            ->setParameter('slug', $slug)
            ->setParameter('start_date', $start)
            ->setParameter('end_date', $end)
            ->orderBy('p.start_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}
