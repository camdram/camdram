<?php
namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Acts\CamdramBundle\Entity\Show;

class PendingAccessRepository extends EntityRepository
{
    /**
     * Find records for pending access based on resource.
     */
    public function findByResource($resource)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.rid = :rid')
            ->andWhere('p.type = :type')
            ->setParameter('rid', $resource->getId());
        if ($resource instanceof Show) {
            $qb->setParameter('type', 'show');
        }
        return $qb->getQuery()->getResult();
    }
}

