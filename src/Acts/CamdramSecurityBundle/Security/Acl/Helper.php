<?php
namespace Acts\CamdramSecurityBundle\Security\Acl;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

use Acts\CamdramSecurityBundle\Entity\Group;
use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\GroupRole;
use Acts\CamdramSecurityBundle\Security\Acl\Permission\MaskBuilder;

class Helper
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var \Symfony\Component\Security\Acl\Model\AclProviderInterface
     */
    private $aclProvider;

    public function __construct(SecurityContextInterface $securityContext, AclProviderInterface $aclProvider)
    {
        $this->securityContext = $securityContext;
        $this->aclProvider = $aclProvider;
    }

    public function isGranted($attributes, $object)
    {
        if (is_string($object)) {
            //asking for a class identity
            $identity = new ObjectIdentity('class', $object);
            return $this->securityContext->isGranted($attributes, $identity);
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

    public function ensureGranted($attributes, $object)
    {
        if (false === $this->isGranted($attributes, $object)) {
            throw new AccessDeniedException();
        }
    }

    public function grantRole($attributes, $object, $identity)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        if ($identity instanceof Group) {
            $identity = new RoleSecurityIdentity((string) new GroupRole($identity));
        }
        elseif (is_string($identity)) {
            $identity = new RoleSecurityIdentity($identity);
        }
        elseif ($identity instanceof User) {
            $identity = UserSecurityIdentity::fromAccount($identity);
        }

        $mask = new MaskBuilder();
        if (is_string($attributes)) $attributes = array($attributes);
        foreach ($attributes as $attribute) $mask->add($attribute);

        try {
            $acl = $this->aclProvider->createAcl($objectIdentity);
        }
        catch (AclAlreadyExistsException $e) {
            $acl = $this->aclProvider->findAcl($objectIdentity);
        }

        $found = false;
        foreach ($acl->getClassAces() as $classAce) {

            if ($classAce->getSecurityIdentity() == $identity) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $acl->insertObjectAce($identity, $mask->get());
            $this->aclProvider->updateAcl($acl);
        }
    }
}