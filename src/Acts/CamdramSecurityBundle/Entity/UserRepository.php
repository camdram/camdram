<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Acts\CamdramBundle\Entity\Show;
use Doctrine\ORM\EntityRepository;
use Acts\CamdramBundle\Entity\Organisation;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;

/**
 * @extends EntityRepository<User>
 */
class UserRepository extends EntityRepository
{

    public function findAdmins($min_level)
    {
        $query = $this->createQueryBuilder('u')
            ->innerJoin('u.aces', 'e')
            ->where('e.type = :type')
            ->andWhere('e.entityId >= :level')
            ->setParameter('level', $min_level)
            ->setParameter('type', 'security')
            ->getQuery();

        return $query->getResult();
    }

    public function findOrganisationAdmins()
    {
        $qb = $this->createQueryBuilder('u');
        $query = $qb->innerJoin('u.aces', 'e')
            ->where('e.type IN (:types)')
            ->setParameter('types', ['society', 'venue', 'security'])
            ->getQuery();

        return $query->getResult();
    }

    public function findActiveUsersForMailOut()
    {
        $qb = $this->createQueryBuilder('u');
        $query = $qb->where('u.is_email_verified = true')
        ->getQuery();

        return $query->getResult();
    }

    public function getEntityOwners(OwnableInterface $entity)
    {
        $query = $this->createQueryBuilder('u')
            ->innerJoin('u.aces', 'e')
            ->where('e.type = :type')
            ->andWhere('e.entityId = :id')
            ->setParameter('id', $entity->getId())
            ->setParameter('type', $entity->getAceType())
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Returns the combined owners of an array of ownable entities.
     */
    public function getOwnersOfEntities(iterable $entities): array
    {
        if (empty($entities)) return [];
        $queryData = [];
        foreach ($entities as $entity) {
            $type = $entity->getAceType();
            if (isset($queryData[$type])) {
                $queryData[$type][] = $entity->getId();
            } else {
                $queryData[$type] = [$entity->getId()];
            }
        }

        $expr = "(e.type = :type0 AND e.entityId IN (:ids0))";
        for ($i = 1; $i < count($queryData); $i++) {
            $expr .= "OR (e.type = :type$i AND e.entityId IN (:ids$i))";
        }
        $qb = $this->createQueryBuilder('u')
            ->innerJoin('u.aces', 'e')
            ->where($expr);
        $i = 0;
        foreach ($queryData as $type => $ids) {
            $qb->setParameter("ids$i", $ids)
               ->setParameter("type$i", $type);
            $i++;
        }
        return $qb->getQuery()->getResult();
    }

    public function getContactableEntityOwners(OwnableInterface $entity)
    {
        $query = $this->createQueryBuilder('u')
        ->innerJoin('u.aces', 'e')
        ->where('e.type = :type')
        ->andWhere('e.entityId = :id')
        ->andWhere('u.is_email_verified = true')
        ->setParameter('id', $entity->getId())
        ->setParameter('type', $entity->getAceType())
        ->getQuery();

        return $query->getResult();
    }

    /**
     * Get the list of users that have requested admin access to a show.
     */
    public function getRequestedShowAdmins(Show $show)
    {
        $query = $this->createQueryBuilder('u')
            ->innerJoin('u.aces', 'e')
            ->where("e.type = 'request-show'")
            ->andWhere('e.entityId = :id')
            ->andWhere('e.grantedBy IS NULL')
            ->setParameter('id', $show->getId())
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Construct a querybuilder (alias u) populated with a search term.
     */
    public function search($query)
    {
        $qb = $this->createQueryBuilder('u');
        $condition = $qb->expr()->orX(
                $qb->expr()->like('u.name', ':query'),
                $qb->expr()->like('u.email', ':query'));
        if (ctype_digit($query)) {
            $condition = $qb->expr()->orX($condition, $qb->expr()->eq('u.id', (int)$query));
        }
        return $qb->where($condition)
            ->setParameter('query', '%' . $query . '%');
    }

    /**
     * Find user by external user details
     */
    public function findByExternalUser($service, $username)
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.external_users', 'e')
            ->where('e.service = :service')
            ->andWhere('e.username = :username')
            ->setParameter('service', $service)
            ->setParameter('username', $username)
            ;
        return $qb->getQuery()->getOneOrNullResult();
    }
}
