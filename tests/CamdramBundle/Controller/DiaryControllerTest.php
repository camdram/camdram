<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Event;
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

    private function createEventWithDate(string $name, int $days, string $time, string $timeto)
    {
        $time = \DateTime::createFromFormat('!H:i', $time);
        $timeto = \DateTime::createFromFormat('!H:i', $timeto);

        $start_date = Time::now();
        $start_date->setTime($time->format('H'), $time->format('i'));
        $day_of_week = $start_date->format('N');
        if ($day_of_week < 7) {
            $start_date->modify('-'.$day_of_week.' days');
        }
        $start_date->modify('+'.$days.' day');

        $event = new Event();
        $event->setDescription("Test event")
              ->setEndTime($timeto)
              ->setName($name)
              ->setStartAt($start_date);

        $this->entityManager->persist($event);
        $this->entityManager->flush();
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
        return $show;
    }

    public function testMain()
    {
        $this->createShowWithDates("Test Show 1", 1, 4, '19:30');
        $this->createShowWithDates("Test Show 2", 2, 1, '14:00');
        $this->entityManager->clear();
        $this->createEventWithDate("Test Event", 3, '14:00', '15:00');

        $crawler = $this->client->request('GET', '/diary');
        $this->assertHTTPStatus(200);
        $this->assertCrawlerHasN('#diary:contains("Test Show 1")', 1, $crawler);
        $this->assertCrawlerHasN('#diary:contains("Test Show 2")', 1, $crawler);
        $this->assertCrawlerHasN('#diary:contains("Test Event")', 1, $crawler);

        //JSON response
        $data = $this->doJsonRequest('/diary.json');
        $this->assertEquals(3, count($data['events']));
        $this->assertArraySubset([
            'events' => [
                [
                    'show' => ['name' => 'Test Show 1'],
                    '_links' => [
                        'show' => '/shows/2000-test-show-1',
                    ],
                    'date_string' => '20:30, Mon 3rd – Thu 6th July 2000',
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
        $this->assertEquals(3, count($vcal->VEVENT));
        $this->assertEquals('Test Show 1', $vcal->VEVENT[0]->SUMMARY);
        $this->assertEquals(new \DateTime('2000-07-03 19:30'), $vcal->VEVENT[0]->DTSTART->getDateTime());
        $this->assertArraySubset(['UNTIL' => '20000706T193000Z'], $vcal->VEVENT[0]->RRULE->getParts());
        $this->assertEquals('Test Show 2', $vcal->VEVENT[1]->SUMMARY);
        $this->assertEquals(new \DateTime('2000-07-04 14:00'), $vcal->VEVENT[1]->DTSTART->getDateTime());
        $this->assertEquals('Test Event', $vcal->VEVENT[2]->SUMMARY);
        $this->assertEquals(new \DateTime('2000-07-05 14:00'), $vcal->VEVENT[2]->DTSTART->getDateTime());
    }

    public function testSpecificDate()
    {
        //View a particular date in the diary

        $this->markTestSkipped('Possible PHP version issue with dates?');

        $show = $this->createShowWithDates("Romeo and Juliet", -7, 2, '19:30');
        $this->assertEquals("2000-07-09 19:30:00 2000-07-10 19:30:00",
            "{$show->getStartAt()->format('Y-m-d H:i:s')} {$show->getEndAt()->format('Y-m-d H:i:s')}");
        $crawler = $this->client->request('GET', '/diary/2000-07-08?end=2000-07-11');
        $this->assertCrawlerHasN('#diary:contains("Romeo and Juliet")', 1, $crawler);

        $data = $this->doJsonRequest('/diary/2000-07-08.json?end=2000-07-11');
        $this->assertEquals(1, count($data['events']));
        $this->assertArraySubset([
            'events' => [
                [
                    'show' => ['name' => 'Romeo and Juliet'],
                    '_links' => [
                        'show' => '/shows/2000-romeo-and-juliet',
                    ],
                    'date_string' => '20:30, Sun 9th – Mon 10th July 2000',
                    'start_at' => '2000-07-09T19:30:00+00:00',
                    'repeat_until' => '2000-07-10',
                ],
            ]
        ], $data);
    }

    public function testSpecificYear()
    {
        //View a particular year in the diary

        $this->markTestSkipped('Possible PHP version issue with dates?');

        $show = $this->createShowWithDates("Hamlet", -30, 7, '19:30');
        $this->assertEquals("2000-08-01 19:30:00 2000-08-07 19:30:00",
            "{$show->getStartAt()->format('Y-m-d H:i:s')} {$show->getEndAt()->format('Y-m-d H:i:s')}");
        $crawler = $this->client->request('GET', '/diary/2000?end=2000-12-30');
        $this->assertCrawlerHasN('#diary:contains("Hamlet")', 1, $crawler);

        $data = $this->doJsonRequest('/diary/2000.json?end=2000-12-30');
        $this->assertEquals(1, count($data['events']));
        $this->assertArraySubset([
            'events' => [
                [
                    'show' => ['name' => 'Hamlet'],
                    '_links' => [
                        'show' => '/shows/2000-hamlet',
                    ],
                    'date_string' => '20:30, Tue 1st – Mon 7th August 2000',
                    'start_at' => '2000-08-01T19:30:00+00:00',
                    'repeat_until' => '2000-08-07',
                ],
            ]
        ], $data);
    }

    public function testSpecificPeriod()
    {
        //View a particular "period" in the diary

        $this->markTestSkipped('Possible PHP version issue with dates?');

        $show = $this->createShowWithDates("Henry V", -14, 2, '19:30');
        $this->assertEquals("2000-07-16 19:30:00 2000-07-17 19:30:00",
            "{$show->getStartAt()->format('Y-m-d H:i:s')} {$show->getEndAt()->format('Y-m-d H:i:s')}");

        $period = new TimePeriod();
        $period->setName("Test Period")
            ->setFullName("Test Period")
            ->setShortName("Test Period")
            ->setStartAt(new \DateTime("2000-06-01"))
            ->setEndAt(new \DateTime("2000-07-15"));
        $this->entityManager->persist($period);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/diary/2000/test-period');
        $this->assertCrawlerHasN('#diary:contains("Henry V")', 1, $crawler);
        $this->assertCrawlerHasN('.diary-period-label:contains("Test Period")', 1, $crawler);

        $data = $this->doJsonRequest('/diary/2000.json?end=2000-12-30');
        $this->assertEquals(1, count($data['events']));
        $this->assertArraySubset([
            'events' => [
                [
                    'show' => ['name' => 'Henry V'],
                    '_links' => [
                        'show' => '/shows/2000-henry-v',
                    ],
                    'date_string' => '20:30, Sun 16th – Mon 17th July 2000',
                    'start_at' => '2000-07-16T19:30:00+00:00',
                    'repeat_until' => '2000-07-17',
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
        $this->assertHTTPStatus(404);

        $this->client->request('GET', '/diary/2003.json?end=invalid-date');
        $this->assertHTTPStatus(400);
    }
}
