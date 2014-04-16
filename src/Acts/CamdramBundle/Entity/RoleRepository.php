<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr as Expr;

/**
 * RoleRepository
 *
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
        return $role->getOrder();
    }

    public function getUpcomingByPerson(\DateTime $now, Person $person)
    {
        $query = $this->createQueryBuilder('r')
            ->join('ActsCamdramBundle:Show', 's', Expr\Join::WITH, 's = r.show')
            ->where('s.start_at >= :now')
            ->andwhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->andWhere('r.person = :person')
            ->orderBy('s.start_at', 'ASC')
            ->setParameter('person', $person)
            ->setParameter('now', $now)
            ->getQuery();
        return $query->getResult();
    }

    public function getCurrentByPerson(\DateTime $now, Person $person)
    {
        $query = $this->createQueryBuilder('r')
            ->join('ActsCamdramBundle:Show', 's', Expr\Join::WITH, 's = r.show')
            ->where('s.end_at >= :now')
            ->andWhere('s.start_at < :now')
            ->andwhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->andWhere('r.person = :person')
            ->orderBy('s.start_at', 'ASC')
            ->setParameter('person', $person)
            ->setParameter('now', $now)
            ->getQuery();
        return $query->getResult();
    }

    public function getPastByPerson(\DateTime $now, Person $person)
    {
        $query = $this->createQueryBuilder('r')
            ->join('ActsCamdramBundle:Show', 's', Expr\Join::WITH, 's = r.show')
            ->where('s.end_at < :now')
            ->andwhere('s.authorised_by is not null')
            ->andWhere('s.entered = true')
            ->andWhere('r.person = :person')
            ->orderBy('s.start_at', 'DESC')
            ->setParameter('person', $person)
            ->setParameter('now', $now)
            ->getQuery();
        return $query->getResult();
    }

    /**
     * Called before removing an entity. Ensure that there are no gaps in the
     * ordering value given to each role.
     */
    public function removeRoleFromOrder($role)
    {
        $query = $this->createQueryBuilder()
            ->update('ActsCamdramBundle:Role', 'r')
            ->set('r.order', 'r.order -1')
            ->where('r.order > :removed_idx')
            ->andWhere('r.type = :type')
            ->setParameters(array('removed_idx' => $role->getOrder(), 'type' => $role->getType()))
            ->getQuery();

        return $query->execute();
    }
}
