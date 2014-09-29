<?php
namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Audition;
use Acts\DiaryBundle\Event\MultiDayEvent;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Show;
use Acts\DiaryBundle\Event\SingleDayEvent;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class DiaryHelper
 *
 * The logic of generating nice-looking diaries with events organised sensibly into rows is done in the DiaryBundle,
 * NOT here. This class contains methods to create diary events from common objects in the Camdram domain, the idea
 * being that DiaryBundle is kept de-coupled and non camdram-specific. It is used by the Diary page as well as the
 * other diaries throughout the site.
 *
 * @package Acts\CamdramBundle\Service
 */

class DiaryHelper
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Generate an event from a 'performance' of a show (which actually represents a range of performances)
     *
     * @param Show $show
     * @param Performance $performance
     * @return MultiDayEvent
     */
    public function createEventsFromPerformance(Performance $performance)
    {
        $event = new MultiDayEvent();
        $event->setName($performance->getShow()->getName());
        $event->setLink($this->router->generate('get_show', array('identifier' => $performance->getShow()->getSlug())));
        $event->setStartTime($performance->getTime());
        $event->setVenue($performance->getVenueName());
        if ($performance->getVenue()) {
            $event->setVenueLink($this->router->generate('get_venue', array('identifier' => $performance->getVenue()->getSlug())));
        }

        if ($performance->getStartDate() < $performance->getExcludeDate() && $performance->getExcludeDate() < $performance->getEndDate()) {
            $end1 = clone $performance->getExcludeDate();
            $end1->modify('-1 day');
            $start2 = clone $performance->getExcludeDate();
            $start2->modify('+1 day');

            $event2 = clone $event;
            $event->setStartDate($performance->getStartDate());
            $event->setEndDate($end1);
            $event2->setStartDate($start2);
            $event2->setEndDate($performance->getEndDate());
            return array($event, $event2);
        }
        else {
            $event->setStartDate($performance->getStartDate());
            $event->setEndDate($performance->getEndDate());
            return array($event);
        }
    }

    /**
     * Generate an array of events corresponding to the various performance ranges of the given show
     *
     * @param array $shows
     * @return array
     */
    public function createEventsFromShows(array $shows)
    {
        $events = array();
        foreach($shows as $show) {
            foreach ($show->getPerformances() as $perf) {
                foreach ($this->createEventsFromPerformance($perf) as $event) {
                    $events[] = $event;
                }
            }
        }
        return $events;
    }

    /**
     * Generate an array of events from an array of performances
     *
     * @param array|Traversible $performances
     * @return array
     */
    public function createEventsFromPerformances($performances)
    {
        if (!is_array($performances) && !$performances instanceof \Traversable) {
            throw new \InvalidArgumentException('$performances must either be an array or a Traversable object');
        }

        $events = array();
        foreach($performances as $performance) {
            foreach ($this->createEventsFromPerformance($performance) as $event) {
                $events[] = $event;
            }
        }
        return $events;
    }

    public function createEventFromAudition(Audition $audition)
    {
        $event = new SingleDayEvent();
        $event->setName($audition->getShow()->getName());
        $event->setLink($this->router->generate('get_auditions').'#auditions-'.$audition->getShow()->getId());
        $event->setDate($audition->getDate());
        $event->setStartTime($audition->getStartTime());
        $event->setEndTime($audition->getEndTime());
        $event->setVenue($audition->getLocation());
        return $event;
    }

    public function createEventsFromAuditions($auditions)
    {
        if (!is_array($auditions) && !$auditions instanceof \Traversable) {
            throw new \InvalidArgumentException('$auditions must either be an array or a Traversable object');
        }

        $events = array();
        foreach ($auditions as $audition) {
            $events[] = $this->createEventFromAudition($audition);
        }
        return $events;
    }
}
