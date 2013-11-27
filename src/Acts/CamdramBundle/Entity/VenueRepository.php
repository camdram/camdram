<?php
namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr as Expr;

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
        $qb = $this->createQueryBuilder('v')->select('DISTINCT(v.name)');
        $qb->innerJoin('ActsCamdramBundle:Show', 's', Expr\Join::WITH, 's.venue = v')
            ->innerJoin('ActsCamdramBundle:Performance', 'p',Expr\Join::WITH, $qb->expr()->andX(
                'p.show = s',
                $qb->expr()->orX(
                    $qb->expr()->andX('p.end_date > :start', 'p.end_date < :end'),
                    $qb->expr()->andX('p.start_date > :start', 'p.start_date < :end'),
                    $qb->expr()->andX('p.start_date < :start', 'p.end_date > :end')
                )
            ))
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        $result = $qb->getQuery()->getResult();
        return count($result);
    }

}