<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Camdram\Tests\RepositoryTestCase;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Advert;

class AdvertRepositoryTest extends RepositoryTestCase
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

    /**
     * @return \Acts\CamdramBundle\Entity\TechieAdvertRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('ActsCamdramBundle:Advert');
    }

    public function testFindNotExpiredOrderedByDateName_before()
    {
        $ad = new Advert();
        $ad->setShow($this->show)
            ->setType(Advert::TYPE_TECHNICAL)
            ->setExpiresAt(new \DateTime('2014-03-12'))
            ->setTitle('Technical Roles')
            ->setSummary("Technical Director\nLighting Designer")
            ->setContactDetails('Contact me')
            ->setBody('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findNotExpiredOrderedByDateName(null, new \DateTime('2014-03-04'));
        $this->assertEquals(1, count($res));
    }

    public function testFindNotExpiredOrderedByDateName_notAuthorised()
    {
        $this->show->setAuthorised(false);

        $ad = new Advert();
        $ad->setShow($this->show)
            ->setType(Advert::TYPE_TECHNICAL)
            ->setExpiresAt(new \DateTime('2014-03-12'))
            ->setTitle('Technical Roles')
            ->setSummary("Technical Director\nLighting Designer")
            ->setContactDetails('Contact me')
            ->setBody('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findNotExpiredOrderedByDateName(null, new \DateTime('2014-03-04'));
        $this->assertEquals(0, count($res));
    }

    public function testFindNotExpiredOrderedByDateName_sameDateBefore()
    {
        $ad = new Advert();
        $ad->setShow($this->show)
            ->setType(Advert::TYPE_TECHNICAL)
            ->setExpiresAt(new \DateTime('2014-03-12 10:00'))
            ->setTitle('Technical Roles')
            ->setSummary("Technical Director\nLighting Designer")
            ->setContactDetails('Contact me')
            ->setBody('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findNotExpiredOrderedByDateName(null, new \DateTime('2014-03-12 09:59'));
        $this->assertEquals(1, count($res));
    }

    public function testFindNotExpiredOrderedByDateName_sameDateAfter()
    {
        $ad = new Advert();
        $ad->setShow($this->show)
            ->setType(Advert::TYPE_TECHNICAL)
            ->setExpiresAt(new \DateTime('2014-03-12 10:05'))
            ->setTitle('Technical Roles')
            ->setSummary("Technical Director\nLighting Designer")
            ->setContactDetails('Contact me')
            ->setBody('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findNotExpiredOrderedByDateName(null, new \DateTime('2014-03-12 10:05'));
        $this->assertEquals(0, count($res));
    }

    public function testFindNotExpiredOrderedByDateName_after()
    {
        $ad = new Advert();
        $ad->setShow($this->show)
            ->setType(Advert::TYPE_TECHNICAL)
            ->setExpiresAt(new \DateTime('2014-03-12'))
            ->setTitle('Technical Roles')
            ->setSummary("Technical Director\nLighting Designer")
            ->setContactDetails('Contact me')
            ->setBody('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findNotExpiredOrderedByDateName(null, new \DateTime('2014-03-15'));
        $this->assertEquals(0, count($res));
    }

    public function testfindLatest()
    {
        $ad = new Advert();
        $ad->setShow($this->show)
            ->setType(Advert::TYPE_TECHNICAL)
            ->setExpiresAt(new \DateTime('2014-03-12'))
            ->setTitle('Technical Roles')
            ->setSummary("Technical Director\nLighting Designer")
            ->setContactDetails('Contact me')
            ->setBody('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findLatest(1, new \DateTime('2014-03-04'));
        $this->assertEquals(1, count($res));
    }

    public function testFindOneById()
    {
        $ad = new Advert();
        $ad->setShow($this->show)
            ->setType(Advert::TYPE_TECHNICAL)
            ->setExpiresAt(new \DateTime('2014-03-12'))
            ->setTitle('Technical Roles')
            ->setSummary("Technical Director\nLighting Designer")
            ->setContactDetails('Contact me')
            ->setBody('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $show = $this->getRepository()->findOneById($ad->getId(), new \DateTime('2014-03-01'));
        $this->assertInstanceOf(Advert::class, $show);
    }
}
