<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\TimePeriod;
use Acts\CamdramBundle\Service\Time;


class DiaryControllerTest extends RestTestCase
{

    public function setUp()
    {
        parent::setUp();

        Time::mockDateTime(new \DateTime('2000-07-03 15:30:00'));
    }

    private function createShowWithDates($show_name, $days, $length, $time)
    {
        $show = new Show();
        $show->setName($show_name)
            ->setCategory('drama')
            ->setAuthorised(true);
        $this->entityManager->persist($show);

        $start_date = Time::now();
        $day_of_week = $start_date->format('N');
        if ($day_of_week < 7) {
            $start_date->modify('-'.$day_of_week.' days');
        }
        $start_date->modify('+'.$days.' day');
        $end_date = clone $start_date;
        $end_date->modify('+'.$length.' days');

        $performance = new Performance();
        $performance->setStartDate($start_date);
        $performance->setEndDate($end_date);
        $performance->setTime(new \DateTime($time));
        $performance->setShow($show);
        $show->addPerformance($performance);

        $this->entityManager->flush();
    }

    public function testMain()
    {
        $this->createShowWithDates("Test Show 1", 1, 4, '19:30');
        $this->createShowWithDates("Test Show 2", 2, 1, '14:00');

        $crawler = $this->client->request('GET', '/diary');
        $this->assertEquals($crawler->filter('#diary:contains("Test Show 1")')->count(), 1);
        $this->assertEquals($crawler->filter('#diary:contains("Test Show 2")')->count(), 1);
    }

    public function testSpecificDate()
    {
        //View a particular date in the diary

        $this->createShowWithDates("Test Show", -7, 2, '19:30');
        $crawler = $this->client->request('GET', '/diary/2000-06-25?end=2000-07-01');
        $this->assertEquals($crawler->filter('#diary:contains("Test Show")')->count(), 1);
    }

    public function testSpecificYear()
    {
        //View a particular year in the diary

        $this->createShowWithDates("Test Show", -30, 7, '19:30');
        $crawler = $this->client->request('GET', '/diary/2000?end=2000-12-30');
        $this->assertEquals($crawler->filter('#diary:contains("Test Show")')->count(), 1);
    }

    public function testSpecificPeriod()
    {
        //View a particular "period" in the diary

        $this->createShowWithDates("Test Show", -14, 2, '19:30');

        $period = new TimePeriod();
        $period->setName("Test Period")
            ->setFullName("Test Period")
            ->setShortName("Test Period")
            ->setStartAt(new \DateTime("2000-06-01"))
            ->setEndAt(new \DateTime("2000-07-15"));
        $this->entityManager->persist($period);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/diary/2000/test-period');
        $this->assertEquals($crawler->filter('#diary:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('.diary-period-label:contains("Test Period")')->count(), 1);
    }

}