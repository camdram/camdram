<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Camdram\Tests\RepositoryTestCase;
use Acts\CamdramBundle\Entity\Audition;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\User;

class AuditionRepositoryTest extends RepositoryTestCase
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
    private function getRepository()
    {
        return $this->em->getRepository('ActsCamdramBundle:Audition');
    }

    public function testFindUpcoming_before()
    {
        $ad = new Audition();
        $ad->setShow($this->show);
        $ad->setDate(new \DateTime('2014-03-12'));
        $ad->setStartTime(new \DateTime('12:00'));
        $ad->setEndTime(new \DateTime('16:00'));
        $ad->setLocation('ADC Theatre Bar');
        $ad->setNonScheduled(false);

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcoming(3, new \DateTime('2014-03-04 12:00'));
        $this->assertEquals(1, count($res));
    }

    public function testFindUpcoming_sameDayBefore()
    {
        $ad = new Audition();
        $ad->setShow($this->show);
        $ad->setDate(new \DateTime('2014-03-12'));
        $ad->setStartTime(new \DateTime('12:00'));
        $ad->setEndTime(new \DateTime('16:00'));
        $ad->setLocation('ADC Theatre Bar');
        $ad->setNonScheduled(false);

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcoming(3, new \DateTime('2014-03-12 13:00'));
        $this->assertEquals(1, count($res));
    }

    public function testFindUpcoming_sameDayAfter()
    {
        $ad = new Audition();
        $ad->setShow($this->show);
        $ad->setDate(new \DateTime('2014-03-12'));
        $ad->setStartTime(new \DateTime('12:00'));
        $ad->setEndTime(new \DateTime('16:00'));
        $ad->setLocation('ADC Theatre Bar');
        $ad->setNonScheduled(false);

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcoming(3, new \DateTime('2014-03-12 17:00'));
        $this->assertEquals(0, count($res));
    }

    public function testFindUpcoming_after()
    {
        $ad = new Audition();
        $ad->setShow($this->show);
        $ad->setDate(new \DateTime('2014-03-12'));
        $ad->setStartTime(new \DateTime('12:00'));
        $ad->setEndTime(new \DateTime('16:00'));
        $ad->setLocation('ADC Theatre Bar');
        $ad->setNonScheduled(false);

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcoming(3, new \DateTime('2014-03-17 13:00'));
        $this->assertEquals(0, count($res));
    }

    public function testFindUpcomingNonScheduled_before()
    {
        $ad = new Audition();
        $ad->setShow($this->show);
        $ad->setDate(new \DateTime('2014-03-12'));
        $ad->setStartTime(new \DateTime('12:00'));
        $ad->setEndTime(new \DateTime('16:00'));
        $ad->setLocation('Contact me');
        $ad->setNonScheduled(true);

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcomingNonScheduled(3, new \DateTime('2014-03-04 12:00'));
        $this->assertEquals(1, count($res));
    }

    public function testFindUpcomingNonScheduled_sameDayBefore()
    {
        $ad = new Audition();
        $ad->setShow($this->show);
        $ad->setDate(new \DateTime('2014-03-12'));
        $ad->setStartTime(new \DateTime('12:00'));
        $ad->setEndTime(new \DateTime('16:00'));
        $ad->setLocation('Contact me');
        $ad->setNonScheduled(true);

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcomingNonScheduled(3, new \DateTime('2014-03-12 13:00'));
        $this->assertEquals(1, count($res));
    }

    public function testFindUpcomingNonScheduled_sameDayAfter()
    {
        $ad = new Audition();
        $ad->setShow($this->show);
        $ad->setDate(new \DateTime('2014-03-12'));
        $ad->setStartTime(new \DateTime('12:00'));
        $ad->setEndTime(new \DateTime('16:00'));
        $ad->setLocation('Contact me');
        $ad->setNonScheduled(true);

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcomingNonScheduled(3, new \DateTime('2014-03-12 17:00'));
        $this->assertEquals(0, count($res));
    }

    public function testFindUpcomingNonScheduled_after()
    {
        $ad = new Audition();
        $ad->setShow($this->show);
        $ad->setDate(new \DateTime('2014-03-12'));
        $ad->setStartTime(new \DateTime('12:00'));
        $ad->setEndTime(new \DateTime('16:00'));
        $ad->setLocation('Contact me');
        $ad->setNonScheduled(true);

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcomingNonScheduled(3, new \DateTime('2014-03-17 13:00'));
        $this->assertEquals(0, count($res));
    }
}
