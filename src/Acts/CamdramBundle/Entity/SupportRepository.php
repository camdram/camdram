<?php

namespace Acts\CamdramBundle\Entity;

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
            ->andWhere("s.state = 'assigned' AND s.support_id = 0")
            ->setParameter('user', $user)
            ->getQuery();
        return $query->getResult();
    }
}

