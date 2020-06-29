<?php

namespace Acts\CamdramBundle\Service;

use Acts\CamdramSecurityBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogProcessor
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function processRecord(array $record)
    {
        $token = $this->tokenStorage->getToken();
        if ($token && ($user = $token->getUser()) instanceof User) {
            $record['extra']['user'] = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            );
        }

        return $record;
    }
}
