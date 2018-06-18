<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SimilarNameRepository extends EntityRepository
{
    public function getEquivalence($name1, $name2)
    {
        $qb = $this->createQueryBuilder('s');
        $query = $qb->where($qb->expr()->andX(
                $qb->expr()->eq('s.name1', ':name1'),
                $qb->expr()->eq('s.name2', ':name2')
            ))->orWhere($qb->expr()->andX(
                $qb->expr()->eq('s.name1', ':name2'),
                $qb->expr()->eq('s.name2', ':name1')
            ))
            ->setParameter('name1', $name1)
            ->setParameter('name2', $name2)
            ->getQuery();

        $res = $query->getOneOrNullResult();
        if ($res) {
            return $res->getEquivalence() ? SimilarName::EQUIVALENT : SimilarName::NOT_EQUIVALENT;
        } else {
            return SimilarName::UNKNOWN;
        }
    }

    public function saveEquivalence($name1, $name2, $equivalent)
    {
        $sn = new SimilarName();
        $sn->setName1($name1);
        $sn->setName2($name2);
        $sn->setEquivalence($equivalent);
        $this->getEntityManager()->persist($sn);
        $this->getEntityManager()->flush();
    }
}
