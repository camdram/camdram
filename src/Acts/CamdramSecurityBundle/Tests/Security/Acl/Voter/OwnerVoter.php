<?php
namespace Acts\CamdramSecurityBundle\Tests\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\OwnerVoter;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class OwnerVoter extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $aclProvider;

    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\OwnerVoter
     */
    private $voter;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken
     */
    private $token;

    public function setUp()
    {
        $this->aclProvider = $this->getMockBuilder('\Acts\\CamdramSecurityBundle\\Security\\Acl\\AclProvider')
            ->disableOriginalConstructor()->getMock();
        $this->voter = new OwnerVoter($this->aclProvider);
        $this->token = new UsernamePasswordToken('testuser', 'password', 'public');
    }

    public function testOwner()
    {
        $show = new Show();

        $this->aclProvider->expects($this->once())->method('isOwner')
            ->with($this->token, $show)->will($this->returnValue(true));

        $this->assertEquals(OwnerVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, $show, array('EDIT')
        ));
    }

    public function testNotOwner()
    {
        $show = new Show();

        $this->aclProvider->expects($this->once())->method('isOwner')
            ->with($this->token, $show)->will($this->returnValue(false));

        $this->assertEquals(OwnerVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $this->token, $show, array('EDIT')
            ));
    }

    public function testOwnerViewUnauthorised()
    {
        $show = new Show();

        $this->aclProvider->expects($this->once())->method('isOwner')
            ->with($this->token, $show)->will($this->returnValue(true));

        $this->assertEquals(OwnerVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, $show, array('VIEW')
            ));
    }

    public function testNotOwnerViewUnauthorised()
    {
        $show = new Show();

        $this->aclProvider->expects($this->once())->method('isOwner')
            ->with($this->token, $show)->will($this->returnValue(false));

        $this->assertEquals(OwnerVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $this->token, $show, array('VIEW')
            ));
    }

}
