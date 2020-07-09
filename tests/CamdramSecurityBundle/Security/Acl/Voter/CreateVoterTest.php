<?php

namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramSecurityBundle\Security\Acl\Voter\CreateVoter;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use PHPUnit\Framework\TestCase;

class CreateVoterTest extends TestCase
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\CreateVoter
     */
    private $voter;

    /**
     * @var OAuthToken
     */
    private $token;

    public function setUp(): void
    {
        $this->voter = new CreateVoter();
        $this->token = new OAuthToken('', []);
        $this->token->setUser('testuser');
    }

    public function testCreateShow()
    {
        $this->assertEquals(CreateVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            \Acts\CamdramBundle\Entity\Show::class,
            array('CREATE')
        ));
    }

    public function testCreateAdvert()
    {
        $this->assertEquals(CreateVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            \Acts\CamdramBundle\Entity\Advert::class,
            array('CREATE')
            ));
    }

    public function testCreateAudition()
    {
        $this->assertEquals(CreateVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            \Acts\CamdramBundle\Entity\Audition::class,
            array('CREATE')
            ));
    }

    public function testNotCreate()
    {
        $this->assertEquals(CreateVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $this->token,
            \Acts\CamdramBundle\Entity\Show::Class,
            array('EDIT')
            ));
    }

    public function testCreateVenue()
    {
        $this->assertEquals(CreateVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $this->token,
            \Acts\CamdramBundle\Entity\Venue::class,
            array('CREATE')
            ));
    }
}
