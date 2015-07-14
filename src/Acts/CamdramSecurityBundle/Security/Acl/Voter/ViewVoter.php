<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Show;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class ViewVoter extends AbstractVoter
{
    protected function getSupportedClasses()
    {
        return array(
            'Acts\\CamdramBundle\\Entity\\Show',
            'Acts\\CamdramBundle\\Entity\\Venue',
            'Acts\\CamdramBundle\\Entity\\Society',
            'Acts\\CamdramBundle\\Entity\\Person'
        );
    }

    protected function getSupportedAttributes()
    {
        return array('VIEW');
    }

    protected function isGranted($attribute, $object, $user = null)
    {
        if ($object instanceof Show) {
            return $object->getAuthorisedBy() !== null;
        }

        return true;
    }
}
