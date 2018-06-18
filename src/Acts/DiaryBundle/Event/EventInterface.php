<?php

namespace Acts\DiaryBundle\Event;

/**
 * Class EventInterface
 *
 * Interface which SingleDayEventInterface and MultiDayEventInterface extend. This shouldn't be used directly - use
 * one of the child classes instead.
 */
interface EventInterface
{
    /**
     * The name of the event
     *
     * @return string
     */
    public function getName();

    /**
     * The name of the venue where the event takes place
     *
     * @return string
     */
    public function getVenue();

    /**
     * The start time of the event. It is represented by a DateTime object, but only the time component is ever used
     *
     * @return \DateTime
     */
    public function getStartTime();

    /**
     * The end time of the event. It is represented by a DateTime object, but only the time component is ever used
     *
     * @return \DateTime
     */
    public function getEndTime();

    /**
     * The URL reached by clicking on the event name (optional)
     *
     * @return null|string
     */
    public function getLink();

    /**
     * The URL reached by clicking on the venue name (optional)
     *
     * @return null|string
     */
    public function getVenueLink();

    /**
     * The date/time at which the information about the event was last updated
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * A unique identifier for the event (as required by the iCal RFC)
     *
     * @return string
     */
    public function getUid();

    /**
     * A description of the event
     *
     * @return string
     */
    public function getDescription();
}
