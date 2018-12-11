<?php

namespace Acts\DiaryBundle\Model;

/**
 * Class EventInterface
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
     * The venue where the event takes place
     *
     * @return VenueInterface
     */
    public function getVenue();

    /**
     * The name of the venue where the event takes place
     *
     * @return string
     */
    public function getVenueName();

    /**
     * The start date and time of the event
     *
     * @return \DateTime
     */
    public function getStartAt();

    /**
     * The end date and time of the event
     * Return null if not end time defined
     *
     * @return \DateTime | null
     */
    public function getEndAt();

    /**
     * For events that repeat daily, the last date on which the event takes place.
     * It is represented by a DateTime object, but only the date component is ever used.
     *
     * @return \DateTime
     */
    public function getRepeatUntil();

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
    public function getId();

}
