<?php

namespace Acts\CamdramAdminBundle\Entity;

use Acts\CamdramSecurityBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

use Doctrine\ORM\Query\Expr;

/**
 * SupportRepository
 *
 */
class SupportRepository extends EntityRepository
{
    /**
     * Get open issues assigned to others.
     */
    public function getOtherUsersIssues(User $user)
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.owner <> :user')
            ->andWhere("s.state = 'assigned' AND s.parent IS NULL")
            ->orderBy('s.id', 'ASC')
            ->setParameter('user', $user)
            ->getQuery();
        return $query->getResult();
    }
}
