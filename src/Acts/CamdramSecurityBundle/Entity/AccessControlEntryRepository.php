<?php
namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Acts\CamdramBundle\Entity\Entity;
use Acts\CamdramBundle\Entity\User;

class AccessControlEntryRepository extends EntityRepository
{

    public function aceExists(User $user, Entity $entity)
    {
        $qb = $this->createQueryBuilder('e');

        $query =$qb->select('COUNT(e.id) AS c')
                ->where('e.user_id = :uid')
                ->andWhere('e.entity_id = :entity_id')
                ->andWhere('e.granted_by IS NOT NULL')
                ->andWhere('e.revoked_by IS NULL')
                ->setParameter('entity_id', $entity->getId())
                ->setParameter('uid', $user->getId())
        ;

        $res = $query->getQuery()->getOneOrNullResult();
        return $res['c'] > 0;

    }

}