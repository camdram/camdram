<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NewsRepository
 */
class NewsRepository extends EntityRepository
{

    public function itemExists($service_name, $id)
    {
        $query = $this->createQueryBuilder('n')
            ->select('count(n.id) AS c')
            ->where('n.source = :name')
            ->andWhere('n.remote_id = :id')
            ->setParameter('name', $service_name)
            ->setParameter('id', (string)$id)
            ->getQuery();
        $result = $query->getResult();
        $count = (int) $result[0]['c'];
        return $count > 0;
    }

    public function getRecent($count)
    {
        $query = $this->createQueryBuilder('n')
            ->orderBy('n.posted_at', 'DESC')
            ->where('n.public = true')
            ->setMaxResults($count)
            ->getQuery();
        return $query->getResult();
    }

    public function getRecentByOrganisation(Organisation $org, $count)
    {
        $query = $this->createQueryBuilder('n')
            ->orderBy('n.posted_at', 'DESC')
            ->where('n.public = true')
            ->andWhere('n.entity = :org')
            ->setParameter('org', $org)
            ->setMaxResults($count)
            ->getQuery();
        return $query->getResult();
    }

}
