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

    public function setUp(): void
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

        $time = \DateTime::createFromFormat('!H:i', $time);
        $start_date = Time::now();
        $start_date->setTime($time->format('H'), $time->format('i'));
        $day_of_week = $start_date->format('N');
        if ($day_of_week < 7) {
            $start_date->modify('-'.$day_of_week.' days');
        }

        $start_date->modify('+'.$days.' day');
        $end_date = clone $start_date;
        $end_date->modify('+'.($length - 1).' days');

        $performance = new Performance();
        $performance->setStartAt($start_date);
        $performance->setRepeatUntil($end_date);
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
                        'show' => '/shows/2000-test-show-1',
                    ],
                    'date_string' => '20:30, Mon 3rd July 2000 - Thu 6th July 2000',
                    'start_at' => '2000-07-03T19:30:00+00:00',
                    'repeat_until' => '2000-07-06',
                ],
                [
                    'show' => ['name' => 'Test Show 2'],
                    '_links' => [
                        'show' => '/shows/2000-test-show-2',
                    ],
                    'date_string' => '15:00, Tue 4th July 2000',
                    'start_at' => '2000-07-04T14:00:00+00:00',
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
                        'show' => '/shows/2000-test-show',
                    ],
                    'date_string' => '20:30, Sun 25th June 2000 - Mon 26th June 2000',
                    'start_at' => '2000-06-25T19:30:00+00:00',
                    'repeat_until' => '2000-06-26',
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
                        'show' => '/shows/2000-test-show',
                    ],
                    'date_string' => '20:30, Fri 2nd June 2000 - Thu 8th June 2000',
                    'start_at' => '2000-06-02T19:30:00+00:00',
                    'repeat_until' => '2000-06-08',
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
                        'show' => '/shows/2000-test-show',
                    ],
                    'date_string' => '20:30, Sun 18th June 2000 - Mon 19th June 2000',
                    'start_at' => '2000-06-18T19:30:00+00:00',
                    'repeat_until' => '2000-06-19',
                ],
            ],
            'labels' => [
                [
                    'text' => 'Test Period',
                    'start_at' => '2000-06-01',
                    'end_at' => '2000-07-15',
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