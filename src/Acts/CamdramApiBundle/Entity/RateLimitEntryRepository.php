<?php

namespace Acts\CamdramApiBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class RateLimitEntryRepository extends EntityRepository
{
    public function count($ip_address)
    {
        $ip = ip2long($ip_address);
        $when = date('Y-m-d H:i:s', strtotime('-1 minute'));
        $query = $this->createQueryBuilder('entry')
            ->select('COUNT(entry.id)')
            ->where('entry.ip_address = :ip')
            ->andWhere('entry.occurred_at > :when')
            ->setParameter('ip', $ip)
            ->setParameter('when', $when)
            ->getQuery();
            return $query->getOneOrNullResult()[1];
    }
}
