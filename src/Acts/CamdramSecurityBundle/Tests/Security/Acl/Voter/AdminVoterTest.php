<?php

namespace Acts\CamdramSecurityBundle\Tests\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\AdminVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\AdminVoter
     */
    private $voter;

    public function setUp()
    {
        $this->voter = new AdminVoter();
    }

    public function testAdmin()
    {
        $token = new UsernamePasswordToken('testuser', 'password', 'public', array('ROLE_ADMIN'));
        $show = new Show();
        $attributes = array('EDIT');
        $this->assertEquals(AdminVoter::ACCESS_GRANTED, $this->voter->vote($token, $show, $attributes));
    }

    public function testNonAdmin()
    {
        $token = new UsernamePasswordToken('testuser', 'password', 'public', array());
        $show = new Show();
        $attributes = array('EDIT');
        $this->assertEquals(AdminVoter::ACCESS_DENIED, $this->voter->vote($token, $show, $attributes));
    }

    public function testNonCamdramObject()
    {
        $token = new UsernamePasswordToken('testuser', 'password', 'public', array('ROLE_ADMIN'));
        $request = new Request();
        $attributes = array('EDIT');
        $this->assertEquals(AdminVoter::ACCESS_ABSTAIN, $this->voter->vote($token, $request, $attributes));
    }
}
