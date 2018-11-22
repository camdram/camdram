<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * RoleRepository
 */
class RoleRepository extends EntityRepository
{
    /**
     * Get the maximum order value for this show and role type.
     */
    public function getMaxOrderByShowType(Show $show, $type)
    {
        $role = $this->findOneBy(
            array('type' => $type, 'show' => $show),
            array('order' => 'DESC')
            );
        $res = 0;
        if ($role != null) {
            // There are no roles of this type.
            $res = $role->getOrder();
        }

        return $res;
    }

    /**
     * Called before removing an entity. Ensure that there are no gaps in the
     * ordering value given to each role.
     */
    public function removeRoleFromOrder($role)
    {
        $query = $this->createQueryBuilder('qb')
            ->update('ActsCamdramBundle:Role', 'r')
            ->set('r.order', 'r.order -1')
            ->where('r.order > :removed_idx')
            ->andWhere('r.type = :type')
            ->setParameters(array('removed_idx' => $role->getOrder(), 'type' => $role->getType()))
            ->getQuery();

        return $query->execute();
    }

}
