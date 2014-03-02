<?php
namespace Acts\CamdramSecurityBundle\Tests\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\CreateVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class CreateVoterTest extends \PHPUnit_Framework_TestCase
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
                $this->token, new ClassIdentity('Acts\\CamdramBundle\\Entity\\Show'), array('CREATE')
        ));
    }

    public function testCreateTechieAdvert()
    {
        $this->assertEquals(CreateVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, new ClassIdentity('Acts\\CamdramBundle\\Entity\\TechieAdvert'), array('CREATE')
            ));
    }

    public function testCreateAudition()
    {
        $this->assertEquals(CreateVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, new ClassIdentity('Acts\\CamdramBundle\\Entity\\Audition'), array('CREATE')
            ));
    }

    public function testCreateApplication()
    {
        $this->assertEquals(CreateVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, new ClassIdentity('Acts\\CamdramBundle\\Entity\\Application'), array('CREATE')
            ));
    }

    public function testNotCreate()
    {
        $this->assertEquals(CreateVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $this->token, new ClassIdentity('Acts\\CamdramBundle\\Entity\\Show'), array('EDIT')
            ));
    }

    public function testCreateVenue()
    {
        $this->assertEquals(CreateVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $this->token, new ClassIdentity('Acts\\CamdramBundle\\Entity\\Venue'), array('CREATE')
            ));
    }

}
