<?php
namespace Acts\CamdramSecurityBundle\Entity;

use Acts\CamdramBundle\Entity\Organisation;
use Doctrine\ORM\EntityRepository;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\EmailBuilder;

class PendingAccessRepository extends EntityRepository
{
    /**
     * Does this match a pre-existing pending access token?
     *
     * This is a lightweight test for equality; is the same email address being
     * granted access to the same resource.
     */
    public function isDuplicate(PendingAccess $ace)
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
        return ($result == null) ? False : True;
    }
    /**
     * Find records for pending access based on resource.
     */
    public function findByResource($resource)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.rid = :rid')
            ->andWhere('p.type = :type')
            ->setParameter('rid', $resource->getId());
<<<<<<< HEAD
        if ($resource instanceof Show) {
            $qb->setParameter('type', 'show');
        } elseif ($resource instanceof Organisation) {
            $qb->setParameter('type', 'society');
        } elseif ($resource instanceof EmailBuilder) {
            $qb->setParameter('type', 'emailBuilder');
=======
        if (($resource instanceof Show) ||
            ($resource instanceof Organisation)) {
            $qb->setParameter('type', $resource->getEntityType());
>>>>>>> 01d821d51bb97c2732c7e43892143c1f19b421f3
        } else {
            return array();
        }
        return $qb->getQuery()->getResult();
    }
}
