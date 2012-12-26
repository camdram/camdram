<?php

namespace Acts\CamdramBundle\Entity;

/**
 * EntityRepository
 */
class CamdramEntityRepository extends \Doctrine\ORM\EntityRepository
{

    public function findWithService($service_name)
    {
        if (!in_array($service_name, array('facebook', 'twitter'))) {
            return array();
        }

        $query = $this->createQueryBuilder('e')
            ->where('e.'.$service_name.'_id is not null')
            ->getQuery();
        return $query->getResult();
    }

}