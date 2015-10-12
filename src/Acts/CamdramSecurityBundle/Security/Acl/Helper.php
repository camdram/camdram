<?php

namespace Acts\CamdramSecurityBundle\Security\Acl;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Helper
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    private $aclProvider;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, AclProvider $aclProvider)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->aclProvider = $aclProvider;
    }

    public function isGranted($attributes, $object = null, $fully_authenticated = true)
    {
        if ($fully_authenticated) {
            if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
                return false;
            }
        }

        return $this->authorizationChecker->isGranted($attributes, $object);
    }

    public function ensureGranted($attributes, $object = null, $fully_authenticated = true)
    {
        if (false === $this->isGranted($attributes, $object, $fully_authenticated)) {
            throw new AccessDeniedException();
        }
    }
}
