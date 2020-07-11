<?php

namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\ViewVoter;
use Acts\CamdramBundle\Service\Time;
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

        Time::mockDateTime(new \DateTime('2000-01-01 12:00'));
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

    public function testViewAdvertHidden()
    {

        $advert = new Advert();
        $advert->setDisplay(false)
            ->setExpiresAt(new \DateTime('2000-01-02 12:00'));
        
        $this->assertEquals(ViewVoter::ACCESS_DENIED, $this->voter->vote(
            $this->token,
            $advert,
            ['VIEW'],
        ));
    }

    public function testViewAdvertExpired()
    {

        $advert = new Advert();
        $advert->setDisplay(true)
            ->setExpiresAt(new \DateTime('1999-12-31 12:00'));
        
        $this->assertEquals(ViewVoter::ACCESS_DENIED, $this->voter->vote(
            $this->token,
            $advert,
            ['VIEW'],

        ));
    }

    public function testViewShowUnauthorised()
    {

        $advert = new Advert();
        $advert->setDisplay(true)
            ->setExpiresAt(new \DateTime('2000-01-02 12:00'));
        
        $show = new Show();
        $show->setAuthorised(false);
        $advert->setShow($show);

        $this->assertEquals(ViewVoter::ACCESS_DENIED, $this->voter->vote(
            $this->token,
            $advert,
            ['VIEW'],
        ));
    }

    public function testViewAdvertVisible()
    {

        $advert = new Advert();
        $advert->setDisplay(true)
            ->setExpiresAt(new \DateTime('2000-01-02 12:00'));
        
        $show = new Show();
        $show->setAuthorised(true);
        $advert->setShow($show);
        
        $this->assertEquals(ViewVoter::ACCESS_GRANTED, $this->voter->vote(
            $this->token,
            $advert,
            ['VIEW'],
        ));
    }
}
