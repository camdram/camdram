<?php
namespace Acts\CamdramBundle\Service;

use Acts\DiaryBundle\Event\MultiDayEvent;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Show;
use Symfony\Component\Routing\RouterInterface;

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

    public function createEventFromPerformance(Show $show, Performance $perf)
    {
        $event = new MultiDayEvent();
        $event->setName($show->getName());
        $event->setStartDate($perf->getStartDate());
        $event->setEndDate($perf->getEndDate());
        $event->setStartTime($perf->getTime());
        $event->setVenue($perf->getVenue());

        $event->setLink($this->router->generate('get_show', array('identifier' => $show->getSlug())));
        if ($show->getVenue() && $perf->getVenue() == $show->getVenue()->getName()) {
            $event->setVenueLink($this->router->generate('get_venue', array('identifier' => $show->getVenue()->getSlug())));
        }
        return $event;
    }

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