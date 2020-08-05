<?php

namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Event\VenueChangeEvent;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This class can listen to all Doctrine events and so can use events like
 * onFlush which cover a whole changeset.
 */
class DoctrineEventListener
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        // Find performance changes to compute changes to venues on shows.
        $isPerformance = function ($obj) { return $obj instanceof Performance; };
        $isShow        = function ($obj) { return $obj instanceof Show; };

        $newPerfs = array_filter($uow->getScheduledEntityInsertions(), $isPerformance);
        $modPerfs = array_filter($uow->getScheduledEntityUpdates(), $isPerformance);
        $delPerfs = array_filter($uow->getScheduledEntityDeletions(), $isPerformance);
        $delShows = array_filter($uow->getScheduledEntityDeletions(), $isShow);

        $shows = self::array_unique_byid(array_map(function ($perf) {
            return $perf->getShow();
        }, array_merge($newPerfs, $modPerfs, $delPerfs)));

        foreach ($shows as $show) {
            // Brand new shows and just-deleted shows aren't relevant
            if ($show->getId() == null || in_array($show, $delShows)) {
                continue;
            }

            // Count how many times the show is performed at each venue.
            $venueCount = [];
            $currentPerformances = $show->getPerformances();
            foreach ($currentPerformances as $perf) {
                $venue = $perf->getVenue();
                if ($venue == null) continue;
                self::array_increment($venueCount, $venue->getId(), 1);
            }
            $currentVenues = array_keys($venueCount);

            // Now work backwards to the number of times the show *was*
            // performed at each venue.
            foreach ($newPerfs as $added) {
                $venue = $added->getVenue();
                if ($venue == null) continue;
                self::array_increment($venueCount, $venue->getId(), -1);
            }
            foreach ($modPerfs as $update) {
                $venueChange = $uow->getEntityChangeSet($update)['venue'] ?? [null, null];
                if ($venueChange[0]) {
                    self::array_increment($venueCount, $venueChange[0]->getId(), 1);
                }
                if ($venueChange[1]) {
                    self::array_increment($venueCount, $venueChange[1]->getId(), -1);
                }
            }
            foreach ($delPerfs as $removed) {
                $venue = $removed->getVenue();
                if ($venue == null) continue;
                self::array_increment($venueCount, $venue->getId(), 1);
            }
            $oldVenues = array_keys(array_filter($venueCount));
            $this->eventDispatcher->dispatch(
                    new VenueChangeEvent($show,
                    /*  added  */ array_diff($currentVenues, $oldVenues),
                    /* removed */ array_diff($oldVenues, $currentVenues)),
                    CamdramSecurityEvents::VENUES_CHANGED
                );
        }
    }


    /**
     * @phpstan-template T
     * @phpstan-param T[] $in
     * @phpstan-return T[]
     * $in is filtered such that only one object of any given id is returned.
     */
    private static function array_unique_byid(array $in): array
    {
        $uniqueIds = array_unique(array_map(function($o) {
            return $o->getId();
        }, $in));
        return array_values(array_intersect_key($in, $uniqueIds));
    }


    /**
     * @param int[] $arr
     * @param int|string $key
     */
    private static function array_increment(array &$arr, $key, int $diff): void
    {
        $arr[$key] = $diff + ($arr[$key] ?? 0);
    }
}
