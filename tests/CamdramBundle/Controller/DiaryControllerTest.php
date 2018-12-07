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
        $end_date->modify('+'.($length - 1).' days');

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

        //JSON response
        $data = $this->doJsonRequest('/diary.json');
        $this->assertEquals(2, count($data['events']));
        $this->assertArraySubset([
            'events' => [
                [
                    'show' => ['name' => 'Test Show 1'],
                    '_links' => [
                        'show' => '/shows/test-show-1',
                    ],
                    'start_date' => (new \DateTime('2000-07-03'))->format('c'),
                    'end_date' => (new \DateTime('2000-07-06'))->format('c'),
                    'time' => (new \DateTime('1970-01-01 19:30'))->format('c'),
                ],
                [
                    'show' => ['name' => 'Test Show 2'],
                    '_links' => [
                        'show' => '/shows/test-show-2',
                    ],
                    'start_date' => (new \DateTime('2000-07-04'))->format('c'),
                    'end_date' => (new \DateTime('2000-07-04'))->format('c'),
                    'time' => (new \DateTime('1970-01-01 14:00'))->format('c'),
                ],
            ]
        ], $data);

        //iCal response
        $vcal = $this->doICalRequest('/diary.ics');
        $this->assertEquals(2, count($vcal->VEVENT));
        $this->assertEquals('Test Show 1', $vcal->VEVENT[0]->SUMMARY);
        $this->assertEquals(new \DateTime('2000-07-03 19:30'), $vcal->VEVENT[0]->DTSTART->getDateTime());
        $this->assertArraySubset(['UNTIL' => '20000706T193000Z'], $vcal->VEVENT[0]->RRULE->getParts());
        $this->assertEquals('Test Show 2', $vcal->VEVENT[1]->SUMMARY);
        $this->assertEquals(new \DateTime('2000-07-04 14:00'), $vcal->VEVENT[1]->DTSTART->getDateTime());
    }

    public function testSpecificDate()
    {
        //View a particular date in the diary

        $this->createShowWithDates("Test Show", -7, 2, '19:30');
        $crawler = $this->client->request('GET', '/diary/2000-06-25?end=2000-07-01');
        $this->assertEquals($crawler->filter('#diary:contains("Test Show")')->count(), 1);

        $data = $this->doJsonRequest('/diary/2000-06-25.json?end=2000-07-01');
        $this->assertEquals(1, count($data['events']));
        $this->assertArraySubset([
            'events' => [
                [
                    'show' => ['name' => 'Test Show'],
                    '_links' => [
                        'show' => '/shows/test-show',
                    ],
                    'start_date' => (new \DateTime('2000-06-25'))->format('c'),
                    'end_date' => (new \DateTime('2000-06-26'))->format('c'),
                    'time' => (new \DateTime('1970-01-01 19:30'))->format('c'),
                ],
            ]
        ], $data);
    }

    public function testSpecificYear()
    {
        //View a particular year in the diary

        $this->createShowWithDates("Test Show", -30, 7, '19:30');
        $crawler = $this->client->request('GET', '/diary/2000?end=2000-12-30');
        $this->assertEquals($crawler->filter('#diary:contains("Test Show")')->count(), 1);

        $data = $this->doJsonRequest('/diary/2000.json?end=2000-12-30');
        $this->assertEquals(1, count($data['events']));
        $this->assertArraySubset([
            'events' => [
                [
                    'show' => ['name' => 'Test Show'],
                    '_links' => [
                        'show' => '/shows/test-show',
                    ],
                    'start_date' => (new \DateTime('2000-06-02'))->format('c'),
                    'end_date' => (new \DateTime('2000-06-08'))->format('c'),
                    'time' => (new \DateTime('1970-01-01 19:30'))->format('c'),
                ],
            ]
        ], $data);
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

        $data = $this->doJsonRequest('/diary/2000.json?end=2000-12-30');
        $this->assertEquals(1, count($data['events']));
        $this->assertArraySubset([
            'events' => [
                [
                    'show' => ['name' => 'Test Show'],
                    '_links' => [
                        'show' => '/shows/test-show',
                    ],
                    'start_date' => (new \DateTime('2000-06-18'))->format('c'),
                    'end_date' => (new \DateTime('2000-06-19'))->format('c'),
                    'time' => (new \DateTime('1970-01-01 19:30'))->format('c'),
                ],
            ],
            'labels' => [
                [
                    'text' => 'Test Period',
                    'start_at' => (new \DateTime('2000-06-01'))->format('c'),
                    'end_at' => (new \DateTime('2000-07-15'))->format('c'),
                ]
            ]
        ], $data);
    }

    public function testInvalidDates()
    {
        $this->client->request('GET', '/diary/blah');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/diary/2003.json?end=invalid-date');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}