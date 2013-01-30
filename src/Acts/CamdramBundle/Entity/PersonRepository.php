<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr as Expr;

/**
 * PersonRepository
 */
class PersonRepository extends EntityRepository
{

    public function findWithSimilarName($name)
    {
        preg_match('/.* ([a-z\'\-]+)$/i', trim($name), $matches);
        if (count($matches) < 2) {
            throw new \InvalidArgumentException(sprintf('An empty name has been provided'));
        }
        $surname = $matches[1];

        $query = $this->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%'.$surname)
            ->getQuery();
        return $query->getResult();
    }

    public function getNumberInDateRange(\DateTime $start, \DateTime $end)
    {
        $qb = $this->createQueryBuilder('e')->select('COUNT(e.id)');
        $qb->innerJoin('ActsCamdramBundle:Role', 'r', Expr\Join::WITH, 'r.person = e')
            ->innerJoin('ActsCamdramBundle:Show', 's', Expr\Join::WITH, 'r.show = s')
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

        $result = $qb->getQuery()->getOneOrNullResult();
        return current($result);
    }

}
