<?php

namespace Acts\CamdramSecurityBundle\Security\Acl;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Helper
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $securityContext;

    private $aclProvider;

    public function __construct(SecurityContextInterface $securityContext, AclProvider $aclProvider)
    {
        $this->securityContext = $securityContext;
        $this->aclProvider = $aclProvider;
    }

    public function isGranted($attributes, $object = null, $fully_authenticated = true)
    {
        if ($fully_authenticated) {
            if (!$this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
                return false;
            }
        }

        return $this->securityContext->isGranted($attributes, $object);
    }

    public function ensureGranted($attributes, $object = null, $fully_authenticated = true)
    {
        if (false === $this->isGranted($attributes, $object, $fully_authenticated)) {
            throw new AccessDeniedException();
        }
    }
}
