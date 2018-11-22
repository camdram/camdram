<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Entity\Person;

class ViewVoter extends Voter
{
    public function supports($attribute, $subject)
    {
        return $attribute == 'VIEW'
            && (
                $subject instanceof Show
                  || $subject instanceof Venue
                  || $subject instanceof Society
                  || $subject instanceof Person
               );
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($subject instanceof Show) {
            return $subject->getAuthorised();
        }

        return true;
    }
}
