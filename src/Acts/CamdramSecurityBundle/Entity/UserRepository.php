<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Acts\CamdramBundle\Entity\Show;
use Doctrine\ORM\EntityRepository;
use Acts\CamdramBundle\Entity\Organisation;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;

class UserRepository extends EntityRepository
{
    public function findByEmailAndPassword($email, $password)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->andWhere('u.password = :password')
            ->setParameter('email', $email)
            ->setParameter('password', md5($password))
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findAdmins($min_level)
    {
        $query = $this->createQueryBuilder('u')
            ->innerJoin('u.aces', 'e')
            ->where('e.type = :type')
            ->andWhere('e.entityId >= :level')
            ->andWhere('e.revokedBy IS NULL')
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
            ->andWhere('e.revokedBy IS NULL')
            ->setParameter('types', ['society', 'venue', 'security'])
            ->getQuery();
        
        return $query->getResult();
    }
    
    public function findActiveUsersForMailOut()
    {
        $loginThreshold = new \DateTime('-2 years');
        
        $qb = $this->createQueryBuilder('u');
        $query = $qb->where('u.is_email_verified = true')
        ->andWhere('u.last_login_at >= :loginThreshold')
        ->setParameter('loginThreshold', $loginThreshold)
        ->getQuery();
        
        return $query->getResult();
    }

    public function findActiveUsersWithoutExternalUser()
    {
        $loginThreshold = new \DateTime('-2 years');

        $qb = $this->createQueryBuilder('u');
        $query = $qb->leftJoin('u.external_users', 'e')
        ->groupBy('u')
        ->having('COUNT(e) = 0')
        ->andWhere($qb->expr()->orX('u.last_login_at >= :loginThreshold', 'u.registered_at >= :loginThreshold'))
        ->andWhere('u.email NOT LIKE :domain')
        ->setParameter('loginThreshold', $loginThreshold)
        ->setParameter('domain', "%@cam.ac.uk")
        ->getQuery();
        
        return $query->getResult();
    }

    public function findActiveCamUsersWithoutExternalUser()
    {
        $loginThreshold = new \DateTime('-2 years');

        $qb = $this->createQueryBuilder('u');
        $query = $qb->leftJoin('u.external_users', 'e')
        ->groupBy('u')
        ->having('COUNT(e) = 0')
        ->andWhere($qb->expr()->orX('u.last_login_at >= :loginThreshold', 'u.registered_at >= :loginThreshold'))
        ->andWhere('u.email LIKE :domain')
        ->setParameter('loginThreshold', $loginThreshold)
        ->setParameter('domain', "%@cam.ac.uk")
        ->getQuery();
        
        return $query->getResult();
    }

    public function findActiveNonCamUsersWithoutExternalUser()
    {
        $loginThreshold = new \DateTime('-2 years');

        $qb = $this->createQueryBuilder('u');
        $query = $qb->leftJoin('u.external_users', 'e')
        ->groupBy('u')
        ->having('COUNT(e) = 0')
        ->andWhere($qb->expr()->orX('u.last_login_at >= :loginThreshold', 'u.registered_at >= :loginThreshold'))
        ->andWhere('u.email NOT LIKE :domain')
        ->setParameter('loginThreshold', $loginThreshold)
        ->setParameter('domain', "%@cam.ac.uk")
        ->getQuery();
        
        return $query->getResult();
    }

    public function getEntityOwners(OwnableInterface $entity)
    {
        $query = $this->createQueryBuilder('u')
            ->innerJoin('u.aces', 'e')
            ->where('e.type = :type')
            ->andWhere('e.entityId = :id')
            ->andWhere('e.revokedBy IS NULL')
            ->setParameter('id', $entity->getId())
            ->setParameter('type', $entity->getAceType())
            ->getQuery();

        return $query->getResult();
    }
    
    public function getContactableEntityOwners(OwnableInterface $entity)
    {
        $query = $this->createQueryBuilder('u')
        ->innerJoin('u.aces', 'e')
        ->where('e.type = :type')
        ->andWhere('e.entityId = :id')
        ->andWhere('e.revokedBy IS NULL')
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
            ->andWhere('e.revokedBy IS NULL')
            ->setParameter('id', $show->getId())
            ->getQuery();

        return $query->getResult();
    }

    /**
     *
     */
    public function search($query, $limit)
    {
        $qb = $this->createQueryBuilder('u');
        $query = $qb
            ->where($qb->expr()->orX($qb->expr()->like('u.name', ':query'), $qb->expr()->like('u.email', ':query')))
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }
}
