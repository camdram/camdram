<?php
namespace Acts\CamdramSecurityBundle\Entity;

use Acts\CamdramBundle\Entity\Organisation;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Acts\CamdramSecurityBundle\Security\User\CamdramUserInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class AccessControlEntryRepository extends EntityRepository
{

    public function aceExists(User $user, OwnableInterface $entity)
    {
        $qb = $this->createQueryBuilder('e');
        $query =$qb->select('COUNT(e.id) AS c')
                ->where('e.user_id = :uid')
                ->andWhere('e.entity_id = :entity_id')
                ->andWhere('e.granted_by IS NOT NULL')
                ->andWhere('e.revoked_by IS NULL')
                ->andWhere('e.type = :type')
                ->setParameter('entity_id', $entity->getId())
                ->setParameter('type', $entity->getAceType())
                ->setParameter('uid', $user->getId())
        ;

        $res = $query->getQuery()->getOneOrNullResult();
        return $res['c'] > 0;
    }

    public function findByUserAndType(User $user, $type)
    {
        $qb = $this->createQueryBuilder('e');
        $query = $qb->where('e.user = :user')
            ->andWhere('e.granted_by IS NOT NULL')
            ->andWhere('e.revoked_by IS NULL')
            ->andWhere('e.type = :type')
            ->setParameter('type', $type)
            ->setParameter('user', $user)
        ;
        return $query->getQuery()->getResult();
    }

}
