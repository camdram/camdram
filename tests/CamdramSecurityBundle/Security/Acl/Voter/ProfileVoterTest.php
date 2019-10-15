<?php

namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\ProfileVoter;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use PHPUnit\Framework\TestCase;

class ProfileVoterTest extends TestCase
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\ProfileVoter
     */
    private $voter;

    /**
     * @var OAuthToken
     */
    private $token;

    /**
     * @var \Acts\CamdramSecurityBundle\Entity\User
     */
    private $user;

    public function setUp(): void
    {
        $this->voter = new ProfileVoter();
        $this->user = new User();
        $this->token = new OAuthToken('', $this->user->getRoles());
        $this->token->setUser($this->user);
    }

    public function testOwnProfile()
    {
        $person = new Person();
        $this->user->setPerson($person);
        $this->assertEquals(ProfileVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            $person,
            array('EDIT')
        ));
    }

    public function testOtherProfile()
    {
        $person1 = new Person();
        $person1->setName('John Smith');
        $person2 = new Person();
        $person2->setName('Joe Bloggs');
        $this->user->setPerson($person2);
        $this->assertEquals(ProfileVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token,
            $person1,
            array('EDIT')
            ));
    }

    public function testDelete()
    {
        $person = new Person();
        $this->user->setPerson($person);
        $this->assertEquals(ProfileVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $this->token,
            $person,
            array('DELETE')
            ));
    }
}
