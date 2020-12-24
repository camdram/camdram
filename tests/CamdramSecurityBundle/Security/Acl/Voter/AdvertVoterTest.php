<?php

namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl\Voter;

use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Component\Security\Core\Security;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\AdvertVoter;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;


class AdvertVoterTest extends TestCase
{

    /**
     * @var MockObject
     */
    private $security;

    /**
     * @var MockObject
     */
    private $aclProvider;

    /**
     * @var AdvertVoter
     */
    private $voter;

    /**
     * @var OAuthToken
     */
    private $token;

    public function setUp() : void
    {
        $this->security = $this->getMockBuilder(Security::class)
            ->disableOriginalConstructor()->getMock();
        $this->aclProvider = $this->getMockBuilder(AclProvider::class)
            ->disableOriginalConstructor()->getMock();

        $this->voter = new AdvertVoter($this->security, $this->aclProvider);
        $this->token = new OAuthToken('', []);
        $this->token->setUser('testuser');
    }

    public function testShowEditor()
    {
        $show = new Show;
        $advert = new Advert;
        $advert->setShow($show);

        $this->security->expects($this->atLeastOnce())->method('isGranted')
            ->with('EDIT', $show)->will($this->returnValue(true));

        $ret = $this->voter->vote($this->token, $advert, ['EDIT']);
        $this->assertEquals(AdvertVoter::ACCESS_GRANTED, $ret);
    }

    public function testNotShowEditor()
    {
        $show = new Show;
        $advert = new Advert;
        $advert->setShow($show);

        $this->security->expects($this->atLeastOnce())->method('isGranted')
            ->with('EDIT', $show)->will($this->returnValue(false));

        $ret = $this->voter->vote($this->token, $advert, ['EDIT']);
        $this->assertEquals(AdvertVoter::ACCESS_DENIED, $ret);
    }
}
