<?php

namespace Acts\CamdramSecurityBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Acts\CamdramSecurityBundle\Security\Acl\Dbal\AclListProvider;

class SecurityUtils
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAclEntries($role, $class_name)
    {
        /** @var $aclProvider AclListProvider */
        $aclProvider = $this->container->get('camdram.security.acl.provider');

        if ($role instanceof \Acts\CamdramSecurityBundle\Entity\User) {
            return $aclProvider->getEntitiesByUser($role, $class_name);
        }

        return array();
    }

    public function isGranted($attributes, $object = null, $fully_authenticated = true)
    {
        return $this->container->get('camdram.security.acl.helper')->isGranted($attributes, $object, $fully_authenticated);
    }

    public function hasRole($role)
    {
        return $this->container->get('security.context')->isGranted($role);
    }

    public function ensureRole($role)
    {
        if (false === $this->hasRole($role)) {
            throw new AccessDeniedException();
        }
    }

    public function isOwner($object)
    {
        $token = $this->container->get('security.context')->getToken();

        return $this->container->get('camdram.security.acl.provider')->isOwner($token, $object);
    }

    public function hasOwners(OwnableInterface $object)
    {
        return $this->container->get('camdram.security.acl.provider')->hasOwners($object);
    }
}
