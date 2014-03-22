<?php

namespace Acts\CamdramBundle\Tests\Service;

use Acts\CamdramBackendBundle\Test\RepositoryTestCase;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\CamdramSecurityBundle\Entity\User;

class TechieAdvertRepositoryTest extends RepositoryTestCase
{

    private $user;

    private $show;

    public function setUp()
    {
        parent::setUp();
        $this->user = new User();
        $this->user->setEmail('test@camdram.net');
        $this->user->setName('Test User');
        $this->em->persist($this->user);

        $this->show = new Show();
        $this->show->setName('Test Show');
        $this->show->setCategory('drama');
        $this->show->setAuthorisedBy($this->user);
        $this->em->persist($this->show);

        $this->em->flush();
    }

    /**
     * @return \Acts\CamdramBundle\Entity\TechieAdvertRepository
     */
    private function getRepository() {
        return $this->em->getRepository('ActsCamdramBundle:TechieAdvert');
    }

    public function testFindNotExpiredOrderedByDateName_before()
    {
        $ad = new TechieAdvert();
        $ad->setShow($this->show);
        $ad->setExpiry(new \DateTime('2014-03-12'));
        $ad->setDeadline(true);
        $ad->setDeadlineTime(new \DateTime('00:00'));
        $ad->setPositions("Technical Director\nLighting Designer");
        $ad->setContact('Contact me');
        $ad->setTechExtra('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findNotExpiredOrderedByDateName(new \DateTime('2014-03-04'));
        $this->assertEquals(1, count($res));
    }

    public function testFindNotExpiredOrderedByDateName_notAuthorised()
    {
        $this->show->setAuthorisedBy(null);

        $ad = new TechieAdvert();
        $ad->setShow($this->show);
        $ad->setExpiry(new \DateTime('2014-03-12'));
        $ad->setDeadline(true);
        $ad->setDeadlineTime(new \DateTime('00:00'));
        $ad->setPositions("Technical Director\nLighting Designer");
        $ad->setContact('Contact me');
        $ad->setTechExtra('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findNotExpiredOrderedByDateName(new \DateTime('2014-03-04'));
        $this->assertEquals(0, count($res));
    }

    public function testFindNotExpiredOrderedByDateName_sameDateBefore()
    {
        $ad = new TechieAdvert();
        $ad->setShow($this->show);
        $ad->setExpiry(new \DateTime('2014-03-12'));
        $ad->setDeadline(true);
        $ad->setDeadlineTime(new \DateTime('10:05'));
        $ad->setPositions("Technical Director\nLighting Designer");
        $ad->setContact('Contact me');
        $ad->setTechExtra('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findNotExpiredOrderedByDateName(new \DateTime('2014-03-12 10:00'));
        $this->assertEquals(1, count($res));
    }

    public function testFindNotExpiredOrderedByDateName_sameDateAfter()
    {
        $ad = new TechieAdvert();
        $ad->setShow($this->show);
        $ad->setExpiry(new \DateTime('2014-03-12'));
        $ad->setDeadline(true);
        $ad->setDeadlineTime(new \DateTime('10:00'));
        $ad->setPositions("Technical Director\nLighting Designer");
        $ad->setContact('Contact me');
        $ad->setTechExtra('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findNotExpiredOrderedByDateName(new \DateTime('2014-03-12 10:05'));
        $this->assertEquals(0, count($res));
    }

    public function testFindNotExpiredOrderedByDateName_after()
    {
        $ad = new TechieAdvert();
        $ad->setShow($this->show);
        $ad->setExpiry(new \DateTime('2014-03-12'));
        $ad->setDeadline(true);
        $ad->setDeadlineTime(new \DateTime('00:00'));
        $ad->setPositions("Technical Director\nLighting Designer");
        $ad->setContact('Contact me');
        $ad->setTechExtra('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findNotExpiredOrderedByDateName(new \DateTime('2014-03-15'));
        $this->assertEquals(0, count($res));
    }


    public function testfindLatest()
    {
        $ad = new TechieAdvert();
        $ad->setShow($this->show);
        $ad->setExpiry(new \DateTime('2014-03-12'));
        $ad->setDeadline(true);
        $ad->setDeadlineTime(new \DateTime('00:00'));
        $ad->setPositions("Technical Director\nLighting Designer");
        $ad->setContact('Contact me');
        $ad->setTechExtra('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findLatest(1, new \DateTime('2014-03-04'));
        $this->assertEquals(1, count($res));
    }


    public function testFindOneByShowSlug()
    {
        $ad = new TechieAdvert();
        $ad->setShow($this->show);
        $ad->setExpiry(new \DateTime('2014-03-12'));
        $ad->setDeadline(true);
        $ad->setDeadlineTime(new \DateTime('00:00'));
        $ad->setPositions("Technical Director\nLighting Designer");
        $ad->setContact('Contact me');
        $ad->setTechExtra('Get involved with this show');

        $this->em->persist($ad);
        $this->em->flush();

        $show = $this->getRepository()->findOneByShowSlug($this->show->getSlug(), new \DateTime('2014-03-01'));
        $this->assertInstanceOf('\\Acts\\CamdramBundle\\Entity\\TechieAdvert', $show);
    }

}
