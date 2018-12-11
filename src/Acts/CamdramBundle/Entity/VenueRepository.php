<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

class VenueRepository extends EntityRepository
{
    public function findAllOrderedByName()
    {
        $query = $this->createQueryBuilder('v')
            ->orderBy('v.name')
            ->getQuery();

        return $query->getResult();
    }

    public function getNumberInDateRange(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('v')->select('COUNT(DISTINCT v.id)')
            ->innerJoin('v.performances', 'p')
            ->innerJoin('p.show', 's')
            ->where('s.authorised = true')
            ->andWhere('p.start_at < :end')
            ->andWhere('p.repeat_until >= :start')
            ->setParameter('start', $start)
            ->setParameter('end', $end);
        $result = $qb->getQuery()->getOneOrNullResult();

        return current($result);
    }
}
