<?php

namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\EditorVoter;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class EditorVoterTest extends TestCase
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\EditorVoter
     */
    private $voter;

    public function setUp(): void
    {
        $this->voter = new EditorVoter();
    }

    private function getEditorUser()
    {
        $user = new User();

        $ace = new AccessControlEntry();
        $ace->setType('security');
        $ace->setEntityId(AccessControlEntry::LEVEL_CONTENT_ADMIN);
        $ace->setGrantedBy(new User());
        $user->addAce($ace);

        return $user;
    }

    public function testEditorCreate()
    {
        $user = $this->getEditorUser();
        $token = new OAuthToken('', $user->getRoles());
        $token->setUser($user);

        $this->assertEquals(EditorVoter::ACCESS_GRANTED, $this->voter->vote(
                $token,
            \Acts\CamdramBundle\Entity\Venue::class,
            array('CREATE')
        ));
    }

    public function testEditorEdit()
    {
        $user = $this->getEditorUser();
        $token = new OAuthToken('', $user->getRoles());
        $token->setUser($user);

        $this->assertEquals(EditorVoter::ACCESS_GRANTED, $this->voter->vote(
                $token,
            new Venue(),
            array('EDIT')
            ));
    }

    public function testNotEditor()
    {
        $token = new OAuthToken('', []);
        $token->setUser(new User());

        $this->assertEquals(EditorVoter::ACCESS_DENIED, $this->voter->vote(
                $token,
            new Venue(),
            array('EDIT')
            ));
    }
}
