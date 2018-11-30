<?php

namespace Acts\DiaryBundle\Diary\Renderer;

use Acts\DiaryBundle\Diary\Diary;
use Sabre\VObject\Component\VCalendar;

/**
 * Class ICalRenderer
 *
 * Takes a Diary object and outputs all the events in iCal format
 */
class ICalRenderer
{
    public function render(Diary $diary)
    {
        $vcalendar = new VCalendar();
        $vcalendar->remove('PRODID');
        $vcalendar->add('PRODID', '-//Camdram//NONSGML Show Diary//EN');

        foreach ($diary->getEvents() as $event) {
            $start_time = null;
            $rrule = array();

            if ($event->getStartDate() == $event->getEndDate()) {
                $start_time = new \DateTime($event->getDate().' '.$event->getStartTime()->format('H:i:s'));
            } else {
                $start_time = new \DateTime($event->getStartDate()->format('Y-m-d').' '.$event->getStartTime()->format('H:i:s'));
                $last_start_time = new \DateTime($event->getEndDate()->format('Y-m-d').' '.$event->getStartTime()->format('H:i:s'));
                $rrule = 'FREQ=DAILY;UNTIL='.$last_start_time->format('Ymd\\THis\\Z');
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
                    'UID' => $event->getId().'@camdram.net',
                    'DTSTAMP' => $dtstamp,
                    'DTSTART' => $start_time,
                    'DURATION' => 'PT2H00M00S',
                    'DESCRIPTION' => $event->getDescription()
                );
                if ($rrule) {
                    $params['RRULE'] = $rrule;
                }
                $vcalendar->add('VEVENT', $params);
            }
        }

        return $vcalendar->serialize();
    }
}
