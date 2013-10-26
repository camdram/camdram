<?php
namespace Acts\CamdramSecurityBundle\Security;

use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Security\User\CamdramUserInterface;
use Acts\ExternalLoginBundle\Security\Authentication\Token\ExternalLoginToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\CamdramBundle\Entity\User;

class UserLinker
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findCamdramToken(TokenInterface $token1, TokenInterface $token2)
    {
        if ($token1 instanceof UsernamePasswordToken) {
            return $token1;
        }
        elseif ($token2 instanceof UsernamePasswordToken) {
            return $token2;
        }
        elseif ($token1 instanceof ExternalLoginToken && $token2 instanceof ExternalLoginToken) {
            if ($token1->getUser()->getUser()) {
                return $token1;
            }
            elseif ($token2->getUser()->getUser()) {
                return $token2;
            }
        }
        return false;
    }

    public function findExternalToken(TokenInterface $token1, TokenInterface $token2)
    {
        if ($token1 instanceof ExternalLoginToken && $token2 instanceof ExternalLoginToken) {
            if (!$token1->getUser()->getUser()) {
                return $token1;
            }
            elseif (!$token2->getUser()->getUser()) {
                return $token2;
            }
        }

        if ($token1 instanceof ExternalLoginToken) {
            return $token1;
        }
        elseif ($token2 instanceof ExternalLoginToken) {
            return $token2;
        }

        return false;
    }

    public function linkUsers(CamdramUserInterface $user1, CamdramUserInterface $user2)
    {
        if ($user1 instanceof ExternalUser && $user1->getUser()) {
            $user1 = $user1->getUser();
        }
        if ($user2 instanceof ExternalUser && $user2->getUser()) {
            $user2 = $user2->getUser();
        }

        if ($user1 instanceof ExternalUser && $user2 instanceof User) {
            $camdram_user = $user2;
            $external_user = $user1;
        }
        elseif ($user1 instanceof User && $user2 instanceof ExternalUser) {
            $camdram_user = $user1;
            $external_user = $user2;
        }

        if (isset($camdram_user) && isset($external_user)) {
            $external_user = $this->entityManager->merge($external_user);
            $camdram_user = $this->entityManager->merge($camdram_user);

            $external_user->setUser($camdram_user);
            $camdram_user->addExternalUser($external_user);
            $this->entityManager->flush();
            return true;
        }
        else return false;
    }
}