<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Camdram\Tests\RepositoryTestCase;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Audition;

class AdvertTest extends RepositoryTestCase
{
    private $user;

    private $show;

    public function setUp(): void
    {
        parent::setUp();

        $this->show = new Show();
        $this->show->setName('Test Show');
        $this->show->setCategory('drama');
        $this->show->setAuthorised(true);
        $this->em->persist($this->show);

        $this->em->flush();
    }

    public function testTechiesAdvert_removesAuditions()
    {
        $ad = new Advert();
        $ad->setShow($this->show)
            ->setType(Advert::TYPE_TECHNICAL)
            ->setTitle('Technical Roles')
            ->setSummary("Technical Director\nLighting Designer")
            ->setContactDetails('Contact me')
            ->setBody('Get involved with this show');

        $audition = new Audition;
        $audition->setStartAt(new \DateTime('2010-03-05 18:00'))
            ->setEndAt(new \DateTime('2010-03-05 20:00'))
            ->setLocation('Location 1');
        $ad->addAudition($audition);

        $this->em->persist($ad);
        $this->em->flush();

        $this->assertEquals(0, count($ad->getAuditions()));
    }

    public function testActorsAdvert_changeType()
    {
        $ad = new Advert();
        $ad->setShow($this->show)
            ->setType(Advert::TYPE_ACTORS)
            ->setTitle('Technical Roles')
            ->setSummary("Technical Director\nLighting Designer")
            ->setContactDetails('Contact me')
            ->setBody('Get involved with this show');

        $audition = new Audition;
        $audition->setStartAt(new \DateTime('2010-03-05 18:00'))
            ->setEndAt(new \DateTime('2010-03-05 20:00'))
            ->setLocation('Location 1');
        $ad->addAudition($audition);
        $this->em->persist($ad);
        $this->em->flush();

        $ad->setType(Advert::TYPE_APPLICATION);
        $this->em->flush();

        $this->assertEquals(0, count($ad->getAuditions()));
    }

}
