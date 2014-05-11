<?php
namespace Acts\CamdramBackendBundle\Logger;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LogProcessor
{

    private $container;

    /**
     * Takes whole service container in order to avoid circular reference, because
     * the security context depends on Doctrine, which depends on the logger...
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    private function getUser()
    {
        $token = $this->container->get('security.context')->getToken();
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
