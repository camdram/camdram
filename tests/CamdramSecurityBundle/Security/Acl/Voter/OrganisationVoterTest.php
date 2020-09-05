<?php

namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Security\Acl\Voter\OrganisationVoter;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Camdram\Tests\RestTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class OrganisationVoterTest extends RestTestCase
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Voter\OrganisationVoter
     */
    private $voter;

    /**
     * @var OAuthToken
     */
    private $token;

    /**
     * @var \Acts\CamdramSecurityBundle\Entity\User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->voter = new OrganisationVoter($this->aclProvider);
        $this->user = $this->createUser();
        $this->token = new OAuthToken('', $this->user->getRoles());
        $this->token->setUser($this->user);
    }

    public function testSocietyOwner()
    {
        $show = new Show();
        $society = new Society();
        $society->setName('Test Society');
        $show->getSocieties()->add($society);
        $this->entityManager->persist($society);
        $this->entityManager->flush();
        $this->aclProvider->grantAccess($society, $this->user);

        $this->assertEquals(OrganisationVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            $show,
            array('EDIT')
        ));
        $this->assertEquals(OrganisationVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            $show,
            array('APPROVE')
            ));
    }

    public function testNotSocietyOwner()
    {
        $show = new Show();
        $society = new Society();
        $society->setName('Test Society');
        $show->getSocieties()->add($society);
        $this->entityManager->persist($society);
        $this->entityManager->flush();

        $this->assertEquals(OrganisationVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token,
            $show,
            array('EDIT')
            ));
        $this->assertEquals(OrganisationVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token,
            $show,
            array('APPROVE')
            ));
    }

    public function testVenueOwner()
    {
        $show = $this->createShow("Little Shop of Horrors");
        $venue = new Venue();
        $venue->setName('Test Venue');
        $show->getPerformances()->first()->setVenue($venue);
        $this->entityManager->persist($venue);
        $this->entityManager->flush();
        $this->aclProvider->grantAccess($venue, $this->user);

        $this->assertEquals(OrganisationVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            $show,
            array('EDIT')
            ));
        $this->assertEquals(OrganisationVoter::ACCESS_GRANTED, $this->voter->vote(
                $this->token,
            $show,
            array('APPROVE')
            ));
    }

    public function testNotVenueOwner()
    {
        $show = $this->createShow("Little Shop of Horrors");
        $venue = new Venue();
        $venue->setName('Test Venue');
        $show->getPerformances()->first()->setVenue($venue);
        $this->entityManager->persist($venue);
        $this->entityManager->flush();

        $this->assertEquals(OrganisationVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token,
            $show,
            array('EDIT')
            ));
        $this->assertEquals(OrganisationVoter::ACCESS_DENIED, $this->voter->vote(
                $this->token,
            $show,
            array('APPROVE')
            ));
    }
}
