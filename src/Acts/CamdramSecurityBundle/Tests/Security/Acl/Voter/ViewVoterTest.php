<?php
namespace Acts\CamdramSecurityBundle\Tests\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\ViewVoter;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ViewVoterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\ViewVoter
     */
    private $voter;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken
     */
    private $token;

    public function setUp()
    {
        $this->voter = new ViewVoter();
        $this->token = new UsernamePasswordToken('testuser', 'password', 'public');
    }

    public function testViewNewShow()
    {
        $this->assertEquals(ViewVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $this->token, new Show(), array('VIEW')
        ));
    }

    public function testViewAuthorisedShow()
    {
        $show = new Show();
        $show->setAuthorisedBy(new User());
        $this->assertEquals(ViewVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, $show, array('VIEW')
            ));
    }

    public function testViewVenue()
    {
        $this->assertEquals(ViewVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token, new Venue(), array('VIEW')
            ));
    }

    public function testNotView()
    {
        $this->assertEquals(ViewVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $this->token, new Venue(), array('EDIT')
            ));
    }

}