<?php

namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\ViewVoter;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use PHPUnit\Framework\TestCase;

class ViewVoterTest extends TestCase
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\ViewVoter
     */
    private $voter;

    /**
     * @var OAuthToken
     */
    private $token;

    public function setUp(): void
    {
        $this->voter = new ViewVoter();
        $this->token = new OAuthToken('', []);
        $this->token->setUser('testuser');
    }

    public function testViewNewShow()
    {
        $this->assertEquals(ViewVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token,
            new Show(),
            array('VIEW')
        ));
    }

    public function testViewAuthorisedShow()
    {
        $show = new Show();
        $show->setAuthorised(true);
        $this->assertEquals(ViewVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            $show,
            array('VIEW')
            ));
    }

    public function testViewVenue()
    {
        $this->assertEquals(ViewVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            new Venue(),
            array('VIEW')
            ));
    }

    public function testNotView()
    {
        $this->assertEquals(ViewVoter::ACCESS_ABSTAIN, $this->voter->vote(
                $this->token,
            new Venue(),
            array('EDIT')
            ));
    }
}
