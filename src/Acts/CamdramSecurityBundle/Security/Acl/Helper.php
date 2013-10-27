<?php
namespace Acts\CamdramSecurityBundle\Security\Acl;

use Acts\ExternalLoginBundle\Security\Authentication\Token\ExternalLoginToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;

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

    public function isGranted($attributes, $object, $fully_authenticated = true)
    {
        if ($fully_authenticated) {
            if (!$this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) return false;
        }

        if (is_string($object)) {
            //asking for a class identity
            return $this->securityContext->isGranted($attributes, $object);
        }
        else {
            $granted = $this->securityContext->isGranted($attributes, $object);
            if ($granted === false) {
                $identity = new ObjectIdentity('class', get_class($object));
                $granted = $this->securityContext->isGranted($attributes, $identity);
            }
            return $granted;
        }
    }

    public function ensureGranted($attributes, $object, $fully_authenticated = true)
    {
        if (false === $this->isGranted($attributes, $object, $fully_authenticated)) {
            throw new AccessDeniedException();
        }
    }
}