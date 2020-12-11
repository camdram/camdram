<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PositionTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method PositionTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method PositionTag[]    findAll()
 * @method PositionTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PositionTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositionTag::class);
    }

    public function findAllOrderedByLengthDesc()
    {
        return $this->createQueryBuilder('t')
            ->orderBy('LENGTH(t.name)', 'DESC')
            ->getQuery()->getResult();
    }

}
