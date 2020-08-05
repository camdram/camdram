<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NewsRepository
 * @extends EntityRepository<News>
 */
class NewsRepository extends EntityRepository
{
    public function itemExists($service_name, $id): bool
    {
        $query = $this->createQueryBuilder('n')
            ->select('count(n.id) AS c')
            ->where('n.source = :name')
            ->andWhere('n.remote_id = :id')
            ->setParameter('name', $service_name)
            ->setParameter('id', (string) $id)
            ->getQuery();
        $result = $query->getResult();
        $count = (int) $result[0]['c'];

        return $count > 0;
    }

    /** @return iterable<News> */
    public function getRecent($count)
    {
        $query = $this->createQueryBuilder('n')
            ->orderBy('n.posted_at', 'DESC')
            ->setMaxResults($count)
            ->getQuery();

        return $query->getResult();
    }

    /** @return iterable<News> */
    public function getRecentByOrganisation(Organisation $org, $count)
    {
        $qb = $this->createQueryBuilder('n')
            ->orderBy('n.posted_at', 'DESC');
        if ($org instanceof Society) {
            $qb = $qb->andWhere('n.society = :org');
        } else if ($org instanceof Venue) {
            $qb = $qb->andWhere('n.venue = :org');
        } else throw new \Exception('Expected Society or Venue.');

        return $qb->setParameter('org', $org)
            ->setMaxResults($count)
            ->getQuery()->getResult();
    }
}
