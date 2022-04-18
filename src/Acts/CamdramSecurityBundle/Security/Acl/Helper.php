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

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param string $attributes
     * @param ?object $object
     */
    public function isGranted($attributes, $object = null, bool $fully_authenticated = true): bool
    {
        if ($fully_authenticated) {
            if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
                return false;
            }
        }

        return $this->authorizationChecker->isGranted($attributes, $object);
    }

    /**
     * @param string $attributes
     * @param ?object $object
     */
    public function ensureGranted($attributes, $object = null, bool $fully_authenticated = true): void
    {
        if (false === $this->isGranted($attributes, $object, $fully_authenticated)) {
            throw new AccessDeniedException();
        }
    }
}
