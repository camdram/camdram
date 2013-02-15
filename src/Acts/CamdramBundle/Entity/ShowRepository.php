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
        $qb = $this->createQueryBuilder('s')->select('COUNT(s.id)');
        $qb->innerJoin('ActsCamdramBundle:Performance', 'p',Expr\Join::WITH, $qb->expr()->andX(
                'p.show = s',
                $qb->expr()->orX(
                    $qb->expr()->andX('p.end_date > :start', 'p.end_date < :end'),
                    $qb->expr()->andX('p.start_date > :start', 'p.start_date < :end'),
                    $qb->expr()->andX('p.start_date < :start', 'p.end_date > :end')
                )
            ))
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        $result = $qb->getQuery()->getOneOrNullResult();
        return current($result);
    }

    public function findByTimePeriod($id)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.time_periods', 'p')
        ->where('p.id = :id')
            ->setParameter('id', $id);
        return $qb->getQuery()->getResult();
    }

}
