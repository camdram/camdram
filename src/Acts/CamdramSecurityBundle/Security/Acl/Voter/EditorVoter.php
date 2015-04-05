<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramBundle\Search\SearchableInterface;
use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Grants access if user is an editor/'content admin'
 */
class EditorVoter extends BaseClassIdentityVoter
{

    protected function getSupportedClasses()
    {
        return array('Acts\\CamdramBundle\\Entity\\Show',
            'Acts\\CamdramBundle\\Entity\\Society',
            'Acts\\CamdramBundle\\Entity\\Venue',
            'Acts\\CamdramBundle\\Entity\\Person',
            'Acts\\CamdramBundle\\Entity\\TechieAdvert',
            'Acts\\CamdramBundle\\Entity\\Audition',
            'Acts\\CamdramBundle\\Entity\\Application',
        );
    }

    protected function getSupportedAttributes()
    {
        return array('EDIT', 'CREATE', 'APPROVE', 'DELETE');
    }

    protected function isGranted($attribute, $object, TokenInterface $token)
    {
        return $this->isInteractiveRequest($token)
            && $this->hasRole($token, 'ROLE_EDITOR');
    }

}
