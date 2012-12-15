<?php
namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function findUsersWithSimilarName($name)
    {
        preg_match('/.* ([a-z\'\-]+)$/i', trim($name), $matches);
        if (count($matches) < 2) {
            throw new \InvalidArgumentException(sprintf('An empty name has been provided'));
        }
        $surname = $matches[1];

        $query = $this->createQueryBuilder('u')
            ->where('u.name LIKE :name')
            ->orderBy('u.login', 'DESC')
            ->setParameter('name', '%'.$surname)
            ->getQuery();
        return $query->getResult();
    }

    public function findByEmailAndPassword($email, $password)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->andWhere('u.password = :password')
            ->setParameter('email', $email)
            ->setParameter('password', md5($password))
            ->getQuery();
        return $query->getOneOrNullResult();
    }

}