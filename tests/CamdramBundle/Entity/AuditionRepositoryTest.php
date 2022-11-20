<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Camdram\Tests\RepositoryTestCase;
use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Audition;
use Acts\CamdramBundle\Entity\Show;

class AuditionRepositoryTest extends RepositoryTestCase
{
    private $user;

    private $show;

    private $advert;

    public function setUp(): void
    {
        parent::setUp();

        $this->show = new Show();
        $this->show->setName('Test Show');
        $this->show->setCategory('drama');
        $this->show->setAuthorised(true);
        $this->em->persist($this->show);


        $this->advert = new Advert;
        $this->advert->setName('New advert')
            ->setSummary('Lorem ipsum')
            ->setBody('Lorem ipsum')
            ->setContactDetails('foo@bar.com')
            ->setShow($this->show);
        $this->em->persist($this->advert);

        $this->em->flush();
    }

    /**
     * @return \Acts\CamdramBundle\Entity\TechieAdvertRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('Acts\\CamdramBundle\\Entity\\Audition');
    }

    public function testFindUpcoming_before()
    {
        $ad = new Audition();
        $ad->setAdvert($this->advert);
        $ad->setStartAt(new \DateTime('2014-03-12 12:00'));
        $ad->setEndAt(new \DateTime('2014-03-12 16:00'));
        $ad->setLocation('ADC Theatre Bar');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcoming(3, new \DateTime('2014-03-04 12:00'));
        $this->assertEquals(1, count($res));
    }

    public function testFindUpcoming_sameDayBefore()
    {
        $ad = new Audition();
        $ad->setAdvert($this->advert);
        $ad->setStartAt(new \DateTime('2014-03-12 12:00'));
        $ad->setEndAt(new \DateTime('2014-03-12 16:00'));
        $ad->setLocation('ADC Theatre Bar');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcoming(3, new \DateTime('2014-03-12 13:00'));
        $this->assertEquals(1, count($res));
    }

    public function testFindUpcoming_sameDayAfter()
    {
        $ad = new Audition();
        $ad->setAdvert($this->advert);
        $ad->setStartAt(new \DateTime('2014-03-12 12:00'));
        $ad->setEndAt(new \DateTime('2014-03-12 16:00'));
        $ad->setLocation('ADC Theatre Bar');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcoming(3, new \DateTime('2014-03-12 17:00'));
        $this->assertEquals(0, count($res));
    }

    public function testFindUpcoming_after()
    {
        $ad = new Audition();
        $ad->setAdvert($this->advert);
        $ad->setStartAt(new \DateTime('2014-03-12 12:00'));
        $ad->setEndAt(new \DateTime('2014-03-12 16:00'));
        $ad->setLocation('ADC Theatre Bar');

        $this->em->persist($ad);
        $this->em->flush();

        $res = $this->getRepository()->findUpcoming(3, new \DateTime('2014-03-17 13:00'));
        $this->assertEquals(0, count($res));
    }

}
