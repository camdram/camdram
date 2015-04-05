<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Acts\CamdramBundle\Entity\Show;
use Symfony\Component\Security\Core\User\User;

/**
 * Grants access if a standard user can create the class
 */
class CreateVoter extends BaseClassIdentityVoter
{
    protected function getSupportedAttributes()
    {
        return array('CREATE');
    }

    protected function getSupportedClasses()
    {
        return array(
            'Acts\\CamdramBundle\\Entity\\Show',
            'Acts\\CamdramBundle\\Entity\\TechieAdvert',
            'Acts\\CamdramBundle\\Entity\\Audition',
            'Acts\\CamdramBundle\\Entity\\Application'
        );
    }

    protected function isGranted($attribute, $object, TokenInterface $token)
    {
        if ($this->isApiRequest($token)) {
            return $this->hasRole($token, 'ROLE_API_WRITE')
                || $this->hasRole($token, 'ROLE_API_WRITE_ORG');
        }
        else {
            return true;
        }
    }

}
