<?php
namespace Acts\CamdramSecurityBundle\Entity;

use Acts\CamdramBundle\Entity\Organisation;
use Doctrine\ORM\EntityRepository;
use Acts\CamdramBundle\Entity\User;
use Doctrine\ORM\Query\Expr;

class AccessControlEntryRepository extends EntityRepository
{

    public function aceExists(User $user, $entity)
    {
        switch ($entity->getType()) {
            case 'show': $type = 'show'; break;
            case 'society':case 'venue': $type = 'society'; break;
            default: $type = '';
        }

        $qb = $this->createQueryBuilder('e');
        $query =$qb->select('COUNT(e.id) AS c')
                ->where('e.user_id = :uid')
                ->andWhere('e.entity_id = :entity_id')
                ->andWhere('e.granted_by IS NOT NULL')
                ->andWhere('e.revoked_by IS NULL')
                ->andWhere('e.type = :type')
                ->setParameter('entity_id', $entity->getId())
                ->setParameter('type', $type)
                ->setParameter('uid', $user->getId())
        ;

        $res = $query->getQuery()->getOneOrNullResult();
        return $res['c'] > 0;

    }

}