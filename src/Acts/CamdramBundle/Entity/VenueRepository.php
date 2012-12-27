<?php
namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

class VenueRepository extends EntityRepository
{
    public function findAllOrderedByName()
    {
        $query = $this->createQueryBuilder('v')
            ->orderBy('v.name')
            ->where('v.type = 1')
            ->getQuery();
        return $query->getResult();
    }

}