<?php
namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\OwnerVoter;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class OwnerVoterTest extends TestCase
{

    /**
     * @var MockObject
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

    public function setUp(): void
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
            ->with('testuser', $show)->will($this->returnValue(true));

        $this->assertEquals(OwnerVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            $show,
            array('EDIT')
        ));
    }

    public function testNotOwner()
    {
        $show = new Show();

        $this->aclProvider->expects($this->once())->method('isOwner')
            ->with('testuser', $show)->will($this->returnValue(false));

        $this->assertEquals(OwnerVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token,
            $show,
            array('EDIT')
            ));
    }

    public function testOwnerViewUnauthorised()
    {
        $show = new Show();

        $this->aclProvider->expects($this->once())->method('isOwner')
            ->with('testuser', $show)->will($this->returnValue(true));

        $this->assertEquals(OwnerVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            $show,
            array('VIEW')
            ));
    }

    public function testNotOwnerViewUnauthorised()
    {
        $show = new Show();

        $this->aclProvider->expects($this->once())->method('isOwner')
            ->with('testuser', $show)->will($this->returnValue(false));

        $this->assertEquals(OwnerVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token,
            $show,
            array('VIEW')
            ));
    }
}
