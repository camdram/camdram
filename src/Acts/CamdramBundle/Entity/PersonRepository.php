<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PersonRepository
 */
class PersonRepository extends EntityRepository
{

    public function findWithSimilarName($name)
    {
        preg_match('/.* ([a-z\'\-]+)$/i', trim($name), $matches);
        if (count($matches) < 2) {
            throw new \InvalidArgumentException(sprintf('An empty name has been provided'));
        }
        $surname = $matches[1];

        $query = $this->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%'.$surname)
            ->getQuery();
        return $query->getResult();
    }

}
