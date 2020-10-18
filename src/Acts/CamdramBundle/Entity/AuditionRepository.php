<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

/**
 * AuditionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 * @extends EntityRepository<Audition>
 */
class AuditionRepository extends EntityRepository
{
    private function getUpcomingQuery($limit, \DateTime $now)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->leftJoin('a.advert', 'v')
            ->leftJoin('v.show', 's')
            ->where('a.end_at > :now')
            ->andWhere('s IS NOT NULL')
            ->andWhere('s.authorised = true')
            ->orderBy('a.start_at')
            ->setParameter('now', $now)
            ->setMaxResults($limit);

        return $qb;
    }

    public function findUpcoming($limit, \DateTime $now)
    {
        return $this->getUpcomingQuery($limit, $now)->getQuery()->getResult();
    }

    public function findUpcomingBySociety(Society $society, $limit, \DateTime $now)
    {
        return $this->getUpcomingQuery($limit, $now)
            ->andWhere(':society MEMBER OF s.societies')->setParameter('society', $society)
            ->getQuery()->getResult();
    }

    public function findUpcomingByVenue(Venue $venue, $limit, \DateTime $now)
    {
        return $this->getUpcomingQuery($limit, $now)
            ->andWhere('EXISTS (SELECT p FROM \Acts\CamdramBundle\Entity\Performance p WHERE p.show = s AND p.venue = :venue)')
            ->setParameter('venue', $venue)
            ->getQuery()->getResult();
    }
}
