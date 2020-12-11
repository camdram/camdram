<?php

namespace Camdram\Tests\CamdramBundle\Entity;

use Camdram\Tests\RepositoryTestCase;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Audition;
use Acts\CamdramBundle\Entity\Position;

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
            ->setName('Technical Roles')
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
            ->setName('Technical Roles')
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

    public function testPositions()
    {
        $position1 = new Position;
        $position1->setName('Technical Director')
            ->addTagName('Technical Director');
        $this->em->persist($position1);
        $position2 = new Position;
        $position2->setName('Lighting Designer')
            ->addTagName('Lighting Designer');
        $this->em->persist($position2);
        $position3 = new Position;
        $position3->setName('Stage Manager')
            ->addTagName('Stage Manager');
        $this->em->persist($position3);
        $position4 = new Position;
        $position4->setName('Director')
            ->addTagName('Director');
        $this->em->persist($position4);
        $this->em->flush();

        $ad = new Advert();
        $ad->setShow($this->show)
            ->setType(Advert::TYPE_TECHNICAL)
            ->setName('Technical Roles')
            ->setSummary("Technical Director\nLighting Designer")
            ->setContactDetails('Contact me')
            ->setBody('Get involved with this show. Stage Manager');

        $this->em->persist($ad);
        $this->em->flush();
        
        $this->assertEquals(3, count($ad->getPositions()));
    }
}
