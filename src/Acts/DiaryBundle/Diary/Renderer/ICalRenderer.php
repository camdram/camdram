<?php
namespace Acts\DiaryBundle\Diary\Renderer;

use Acts\DiaryBundle\Diary\Diary;
use Acts\DiaryBundle\Event\EventInterface;
use Acts\DiaryBundle\Event\MultiDayEventInterface;
use Acts\DiaryBundle\Event\SingleDayEventInterface;

use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Document;

/**
 * Class ICalRenderer
 *
 * Takes a Diary object and outputs all the events in iCal format
 *
 * @package Acts\DiaryBundle\Diary\Renderer
 */
class ICalRenderer
{
    public function render(Diary $diary)
    {

        /*
         *  BEGIN:VTIMEZONE
                TZID:Europe/London
                BEGIN:DAYLIGHT
                    TZOFFSETFROM:+0000
                    RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
                    DTSTART:19810329T010000
                    TZNAME:GMT+01:00
                    TZOFFSETTO:+0100
                END:DAYLIGHT
                BEGIN:STANDARD
                    TZOFFSETFROM:+0100
                    RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
                    DTSTART:19961027T020000
                    TZNAME:GMT
                    TZOFFSETTO:+0000
                END:STANDARD
            END:VTIMEZONE
         */


        $vcalendar = new VCalendar();
        $vcalendar->remove('PRODID');
        $vcalendar->add('PRODID', '-//Camdram//NONSGML Show Diary//EN');

        foreach ($diary->getEvents() as $event) {
            $start_time = null;
            $rrule = array();

            if ($event instanceof MultiDayEventInterface) {
                $start_time = new \DateTime($event->getStartDate()->format('Y-m-d').' '.$event->getStartTime()->format('H:i:s'));
                $last_start_time = new \DateTime($event->getEndDate()->format('Y-m-d').' '.$event->getStartTime()->format('H:i:s'));
                $rrule = 'FREQ=DAILY;UNTIL='.$last_start_time->format('Ymd\\THis\\Z');
            }
            elseif ($event instanceof SingleDayEventInterface) {
                $start_time = new \DateTime($event->getDate().' '.$event->getStartTime()->format('H:i:s'));
            }

            if ($start_time) {
                $utc = new \DateTimeZone('UTC');
                $start_time->setTimezone($utc);
                $end_time = clone $start_time;
                $end_time->modify('+2 hours');
                $dtstamp = clone $event->getUpdatedAt();
                $dtstamp->setTimezone($utc);

                $params = array(
                    'SUMMARY' => $event->getName(),
                    'LOCATION' => $event->getVenue(),
                    'UID' => $event->getUid(),
                    'DTSTAMP' => $dtstamp,
                    'DTSTART' => $start_time,
                    'DURATION' => 'PT2H00M00S',
                    'DESCRIPTION' => $event->getDescription(),
                    'URL' => $event->getLink(),
                );
                if ($rrule) {
                    $params['RRULE'] = $rrule;
                }
                $vevent = $vcalendar->add('VEVENT', $params);
            }
        }

        return $vcalendar->serialize();
    }
}