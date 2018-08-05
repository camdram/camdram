<?php

namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramSecurityBundle\Security\Acl\Voter\CreateVoter;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use PHPUnit\Framework\TestCase;

class CreateVoterTest extends TestCase
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\CreateVoter
     */
    private $voter;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken
     */
    private $token;

    public function setUp()
    {
        $this->voter = new CreateVoter();
        $this->token = new UsernamePasswordToken('testuser', 'password', 'public');
    }

    public function testCreateShow()
    {
        $this->assertEquals(CreateVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            \Acts\CamdramBundle\Entity\Show::class,
            array('CREATE')
        ));
    }

    public function testCreateTechieAdvert()
    {
        $this->assertEquals(CreateVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            \Acts\CamdramBundle\Entity\TechieAdvert::class,
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

    public function testCreateApplication()
    {
        $this->assertEquals(CreateVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            \Acts\CamdramBundle\Entity\Application::class,
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
