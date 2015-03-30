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
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(DISTINCT e.id)')
            ->innerJoin('e.roles', 'r')
            ->innerJoin('r.show', 's')
            ->innerJoin('s.performances', 'p')
            ->andWhere('p.start_date < :end')
            ->andWhere('p.end_date >= :start')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        $result = $qb->getQuery()->getOneOrNullResult();
        return current($result);
    }

    public function findCanonicalPerson($name){
        $person = $this->createQueryBuilder('p')
            ->leftJoin('p.mapped_to', 'm')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->orderBy('p.id')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
        if ($person && $person->getMappedTo() instanceof Person) {
            return $person->getMappedTo();
        }
        else {
            return $person;
        }
    }

}
