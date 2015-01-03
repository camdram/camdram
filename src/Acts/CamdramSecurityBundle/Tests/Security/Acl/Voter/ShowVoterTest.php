<?php
namespace Acts\CamdramSecurityBundle\Tests\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\ShowVoter;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ShowVoterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $aclProvider;

    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\ShowVoter
     */
    private $voter;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken
     */
    private $token;

    /**
     * @var \Acts\CamdramSecurityBundle\Entity\User
     */
    private $user;

    public function setUp()
    {
        $this->aclProvider = $this->getMockBuilder('\Acts\\CamdramSecurityBundle\\Security\\Acl\\AclProvider')
            ->disableOriginalConstructor()->getMock();
        $this->voter = new ShowVoter($this->aclProvider);
        $this->user = new User();
        $this->token = new UsernamePasswordToken($this->user, 'password', 'public', $this->user->getRoles());
    }

    public function testSocietyOwner()
    {
        $show = new Show();
        $society = new Society();
        $society->setName('Test Society');
        $show->setSociety($society);

        $this->aclProvider->expects($this->any())->method('isOwner')
            ->with($this->user, $society)->will($this->returnValue(true));

        $this->assertEquals(ShowVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, $show, array('EDIT')
        ));
        $this->assertEquals(ShowVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, $show, array('APPROVE')
            ));
    }

    public function testNotSocietyOwner()
    {
        $show = new Show();
        $society = new Society();
        $society->setName('Test Society');
        $show->setSociety($society);

        $this->aclProvider->expects($this->atLeastOnce())->method('isOwner')
            ->with($this->user, $society)->will($this->returnValue(false));

        $this->assertEquals(ShowVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token, $show, array('EDIT')
            ));
        $this->assertEquals(ShowVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token, $show, array('APPROVE')
            ));
    }

    public function testVenueOwner()
    {
        $show = new Show();
        $venue = new Venue();
        $venue->setName('Test Venue');
        $show->setVenue($venue);

        $this->aclProvider->expects($this->atLeastOnce())->method('isOwner')
            ->with($this->user, $venue)->will($this->returnValue(true));

        $this->assertEquals(ShowVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, $show, array('EDIT')
            ));
        $this->assertEquals(ShowVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, $show, array('APPROVE')
            ));
    }

    public function testNotVenueOwner()
    {
        $show = new Show();
        $venue = new Venue();
        $venue->setName('Test Venue');
        $show->setVenue($venue);

        $this->aclProvider->expects($this->atLeastOnce())->method('isOwner')
            ->with($this->user, $venue)->will($this->returnValue(false));

        $this->assertEquals(ShowVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token, $show, array('EDIT')
            ));
        $this->assertEquals(ShowVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token, $show, array('APPROVE')
            ));
    }

}
