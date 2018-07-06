<?php

namespace Acts\CamdramBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LogProcessor
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    private function getUser()
    {
        $token = $this->tokenStorage->getToken();
        if ($token && $token->getUser() instanceof UserInterface) {
            return $token->getUser();
        }
    }

    public function processRecord(array $record)
    {
        if (($user = $this->getUser()) instanceof UserInterface) {
            $record['extra']['user'] = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            );
        }

        return $record;
    }
}
