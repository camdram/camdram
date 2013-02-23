<?php
namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Acts\CamdramBundle\Entity\Entity;
use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\Group;

class AccessControlEntryRepository extends EntityRepository
{

    public function aceExists($users, $groups, Entity $entity)
    {
        $qb = $this->createQueryBuilder('e');

        $user_ids = array();
        $group_ids = array();
        foreach ($users as $i => $user) $user_ids = $user->getId();
        foreach ($groups as $i => $group) $group_ids = $group->getId();

        $query =$qb->select('COUNT(e.id) AS c')
                ->where($qb->expr()->orX($qb->expr()->in('e.user_id', $user_ids),
                    $qb->expr()->in('e.group_id', $group_ids)))
                ->andWhere('e.entity = :entity')
                ->andWhere('e.granted_by IS NOT NULL')
                ->andWhere('e.revoked_by IS NULL')
                ->setParameter('entity', $entity)
                ;

        $res = $query->getQuery()->getOneOrNullResult();
        return $res['c'] > 0;

    }

}