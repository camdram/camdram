<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Camdram\Tests\RepositoryTestCase;
use Acts\CamdramBundle\Entity\Application;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\User;

class ApplicationRepositoryTest extends RepositoryTestCase
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
        return $this->em->getRepository('ActsCamdramBundle:Application');
    }

    public function testFindUpcoming_before()
    {
        $ad = new Application();
        $ad->setShow($this->show);
        $ad->setDeadlineDate(new \DateTime('2014-03-12'));
        $ad->setDeadlineTime(new \DateTime('16:00'));
        $ad->setText('Some text');
        $ad->setFurtherInfo('Some contact details');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findLatest(3, new \DateTime('2014-03-04 12:00'));
        $this->assertEquals(1, count($res));
    }

    public function testFindUpcoming_sameDayBefore()
    {
        $ad = new Application();
        $ad->setShow($this->show);
        $ad->setDeadlineDate(new \DateTime('2014-03-12'));
        $ad->setDeadlineTime(new \DateTime('16:00'));
        $ad->setText('Some text');
        $ad->setFurtherInfo('Some contact details');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findLatest(3, new \DateTime('2014-03-12 13:00'));
        $this->assertEquals(1, count($res));
    }

    public function testFindUpcoming_sameDayAfter()
    {
        $ad = new Application();
        $ad->setShow($this->show);
        $ad->setDeadlineDate(new \DateTime('2014-03-12'));
        $ad->setDeadlineTime(new \DateTime('16:00'));
        $ad->setText('Some text');
        $ad->setFurtherInfo('Some contact details');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findLatest(3, new \DateTime('2014-03-12 17:00'));
        $this->assertEquals(0, count($res));
    }

    public function testFindUpcoming_after()
    {
        $ad = new Application();
        $ad->setShow($this->show);
        $ad->setDeadlineDate(new \DateTime('2014-03-12'));
        $ad->setDeadlineTime(new \DateTime('16:00'));
        $ad->setText('Some text');
        $ad->setFurtherInfo('Some contact details');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findLatest(3, new \DateTime('2014-03-17 13:00'));
        $this->assertEquals(0, count($res));
    }
}
