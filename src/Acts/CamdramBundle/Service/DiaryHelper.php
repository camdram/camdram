<?php
namespace Acts\CamdramBundle\Service;

use Acts\DiaryBundle\Event\MultiDayEvent;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Show;
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
     * @param Performance $perf
     * @return MultiDayEvent
     */
    public function createEventFromPerformance(Show $show, Performance $perf)
    {
        $event = new MultiDayEvent();
        $event->setName($show->getName());
        $event->setStartDate($perf->getStartDate());
        $event->setEndDate($perf->getEndDate());
        $event->setStartTime($perf->getTime());
        $event->setVenue($perf->getVenueName());

        $event->setLink($this->router->generate('get_show', array('identifier' => $show->getSlug())));
        if ($show->getVenue() && $perf->getVenue() == $show->getVenue()->getName()) {
            $event->setVenueLink($this->router->generate('get_venue', array('identifier' => $show->getVenue()->getSlug())));
        }
        return $event;
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
                $event = $this->createEventFromPerformance($show, $perf);
                $events[] = $event;
            }
        }
        return $events;
    }

    /**
     * Generate an array of events from an array of performances
     *
     * @param array $performances
     * @return array
     */
    public function createEventsFromPerformances(array $performances)
    {
        $events = array();
        foreach($performances as $performance) {
            $event = $this->createEventFromPerformance($performance->getShow(), $performance);
            $events[] = $event;
        }
        return $events;
    }
}