<?php

namespace Acts\CamdramBundle\Entity;
use Acts\CamdramSecurityBundle\Entity\Group;

/**
 * EntityRepository
 */
class CamdramEntityRepository extends \Doctrine\ORM\EntityRepository
{

    public function findWithService($service_name)
    {
        if (!in_array($service_name, array('facebook', 'twitter'))) {
            return array();
        }

        $query = $this->createQueryBuilder('e')
            ->where('e.'.$service_name.'_id is not null')
            ->getQuery();
        return $query->getResult();
    }

    public function getByGroup(Group $group, $class_name = null)
    {
        $qb = $this->createQueryBuilder('e');
        $query = $qb
            ->leftJoin('e.aces', 'a')
            ->where('a.group = :group')
            ->setParameter('group', $group);

        if ($class_name) {
            $query->andWhere('e INSTANCE OF '.$class_name)
            ;
        }
        return $query->getQuery()->getResult();
    }

    public function getByUser(User $user, $class_name = null)
    {
        $qb = $this->createQueryBuilder('e');
        $query = $qb
            ->leftJoin('e.aces', 'a')
            ->where('a.user = :user')
            ->setParameter('user', $user);

        if ($class_name) {
            $query->andWhere('e INSTANCE OF '.$class_name)
            ;
        }
        return $query->getQuery()->getResult();
    }

}