<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Entity\Person;

class ProfileVoter extends BaseVoter
{
    /**
     * Return an array of supported classes. This will be called by supportsClass
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    protected function getSupportedClasses()
    {
        return array('Acts\\CamdramBundle\\Entity\\Person');
    }

    /**
     * Return an array of supported attributes. This will be called by supportsAttribute
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    protected function getSupportedAttributes()
    {
        return array('EDIT');
    }


    public function isGranted($attribute, $object, TokenInterface $token)
    {
        $user = $token->getUser();
        return $user instanceof User && $user->getPerson() == $object;
    }

}
