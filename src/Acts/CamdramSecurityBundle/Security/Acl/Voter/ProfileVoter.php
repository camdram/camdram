<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Entity\Person;

class ProfileVoter extends Voter
{
    public function supports($attribute, $subject)
    {
        return $attribute == 'EDIT'
                  && $subject instanceof Person;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        return $user instanceof User && $user->getPerson() == $subject;
    }
}
