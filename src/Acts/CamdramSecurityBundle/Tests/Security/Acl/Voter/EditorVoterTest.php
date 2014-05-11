<?php
namespace Acts\CamdramSecurityBundle\Tests\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\EditorVoter;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class EditorVoterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\EditorVoter
     */
    private $voter;

    public function setUp()
    {
        $this->voter = new EditorVoter();
    }

    public function testEditorCreate()
    {
        $token = new UsernamePasswordToken('testuser', 'password', 'public', array('ROLE_EDITOR'));

        $this->assertEquals(EditorVoter::ACCESS_GRANTED, $this->voter->vote(
                $token, new ClassIdentity('Acts\\CamdramBundle\\Entity\\Venue'), array('CREATE')
        ));
    }

    public function testEditorEdit()
    {
        $token = new UsernamePasswordToken('testuser', 'password', 'public', array('ROLE_EDITOR'));

        $this->assertEquals(EditorVoter::ACCESS_GRANTED, $this->voter->vote(
                $token, new Venue(), array('EDIT')
            ));
    }

    public function testNotEditor()
    {
        $token = new UsernamePasswordToken('testuser', 'password', 'public', array());

        $this->assertEquals(EditorVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $token, new Venue(), array('EDIT')
            ));
    }

}
