<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

abstract class BaseClassIdentityVoter extends BaseVoter {
    public function supportsClass($class)
    {
        return $class == 'Acts\\CamdramSecurityBundle\\Security\\Acl\\ClassIdentity' || parent::supportsClass($class);
    }


    public function supportsClassIdentity($object)
    {
        if ($object instanceof ClassIdentity) {
            return $this->supportsClass($object->getClassName());
        } else {
            return $this->supportsClass(get_class($object));
        }
    }

    /**
     * Iteratively check all given attributes by calling isGranted
     *
     * Same as parent, except check for class identity too
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param object         $object     The object to secure
     * @param array          $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        //Further limit if the class identity does not match
        if (!$this->supportsClassIdentity($object)) {
            return self::ACCESS_ABSTAIN;
        }

        return parent::vote($token, $object, $attributes);
    }

} 