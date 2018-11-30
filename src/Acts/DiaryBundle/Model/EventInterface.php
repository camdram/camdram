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
     * The first date on which the event takes place
     *
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * The last date on which the event takes place
     *
     * @return \DateTime
     */
    public function getEndDate();

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
