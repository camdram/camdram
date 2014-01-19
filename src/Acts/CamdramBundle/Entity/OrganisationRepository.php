<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr as Expr;

/**
 * OrganisationRepository
 */
class OrganisationRepository extends EntityRepository
{

    public function findWithService($service)
    {
        $qb = $this->createQueryBuilder('o');

        switch ($service) {
            case 'facebook': $qb->where('o.facebook_id IS NOT NULL'); break;
            case 'twitter': $qb->where('o.twitter_id IS NOT NULL'); break;
        }

        return $qb->getQuery()->getResult();
    }
}
