<?php
namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupRepository extends EntityRepository
{

    public function findByUser(UserInterface $user)
    {
        $qb = $this->createQueryBuilder('g');
        $qb->join('g.users', 'u', 'WITH', $qb->expr()->in('u.id', $user->getId()));
        $result = $qb->getQuery()->execute();
        return $result;
    }

}