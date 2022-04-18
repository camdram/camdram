<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Event;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Entity\Person;

class ViewVoter extends Voter
{
    public function supports($attribute, $subject): bool
    {
        return $attribute == 'VIEW'
            && (
                $subject instanceof Show
                  || $subject instanceof Venue
                  || $subject instanceof Society
                  || $subject instanceof Person
                  || $subject instanceof Advert
                  || $subject instanceof Event
               );
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if ($subject instanceof Show) {
            return $subject->getAuthorised();
        }

        if ($subject instanceof Advert) {
            if ($subject->getShow() && !$subject->getShow()->getAuthorised()) {
                return false;
            } else {
                return $subject->isVisible();
            }
        }

        return true;
    }
}
