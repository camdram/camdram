<?php

namespace Acts\CamdramBundle\Entity;

use Doctring\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

/**
 * AdvertRepository
 *
 * @extends EntityRepository<Advert>
 */
class AdvertRepository extends EntityRepository
{
    public function findNotExpiredOrderedByDateName($filter, \DateTime $date)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->leftJoin('a.show', 's')
            ->where('a.expiresAt > :expires')
            ->andWhere('a.display = true')
            ->andWhere($qb->expr()->orX('s.authorised = true', 's IS NULL'))
            ->orderBy('a.createdAt DESC, s.name')
            ->setParameter('expires', $date)
            ;

        if ($filter) {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $filter)
            ;
        }

        return $qb->getQuery()->getResult();
    }

    private function getLatestQuery($limit, \DateTime $now)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->leftJoin('a.show', 's')
            ->where('a.expiresAt > :expires')
            ->andWhere('a.display = true')
            ->andWhere($qb->expr()->orX('s.authorised = true', 's IS NULL'))
            ->orderBy('a.createdAt', 'DESC')
            ->setParameter('expires', $now)
            ->setMaxResults($limit);
    }

    public function findLatest($limit, \DateTime $now)
    {
        return $this->getLatestQuery($limit, $now)->getQuery()->getResult();
    }

    public function findLatestBySociety(Society $society, $limit, \DateTime $now)
    {
        $qb = $this->getLatestQuery($limit, $now);
        $qb->andWhere($qb->expr()->orX('a.society = :society', ':society MEMBER OF s.societies')
            )->setParameter('society', $society);

        return $qb->getQuery()->getResult();
    }

    public function findLatestByVenue(Venue $venue, $limit, \DateTime $now)
    {
        $qb = $this->getLatestQuery($limit, $now);

        return $qb->andWhere($qb->expr()->orX('a.venue = :venue',
            'EXISTS (SELECT p FROM \Acts\CamdramBundle\Entity\Performance p WHERE p.show = s AND p.venue = :venue)'))
            ->setParameter('venue', $venue)->getQuery()->getResult();
    }
}
