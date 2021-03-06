<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Acts\CamdramBundle\Entity\Organisation;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Doctrine\ORM\EntityRepository;
use Acts\CamdramBundle\Entity\Show;

/**
 * @extends EntityRepository<PendingAccess>
 */
class PendingAccessRepository extends EntityRepository
{
    /**
     * Does this match a pre-existing pending access token?
     *
     * This is a lightweight test for equality; is the same email address being
     * granted access to the same resource.
     */
    public function isDuplicate(PendingAccess $ace): bool
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.rid = :rid')
            ->andWhere('p.type = :type')
            ->andWhere('p.email = :email')
            ->setParameters(array(
                'rid' => $ace->getRid(),
                'type' => $ace->getType(),
                'email' => $ace->getEmail()
                ));
        $result = $qb->getQuery()->getOneOrNullResult();

        return $result != null;
    }
    /**
     * Find records for pending access based on resource.
     * @return iterable<PendingAccess>
     */
    public function findByResource(OwnableInterface $resource)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.rid = :rid')
            ->andWhere('p.type = :type')
            ->setParameter('rid', $resource->getId())
            ->setParameter('type', $resource->getAceType());
        return $qb->getQuery()->getResult();
    }
}
