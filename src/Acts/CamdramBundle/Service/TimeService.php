<?php
namespace Acts\CamdramBundle\Service;

/**
 * Class TimeService
 * @package Acts\CamdramBundle\Service
 *
 * A class used to source the current time, which can be overridden for testing purposes
 */
class TimeService
{

    private $current_time;

    public function setCurrentTime($time)
    {
        if ($time !== null) $this->current_time = new \DateTime($time);
    }

    public function getCurrentTime()
    {
        if ($this->current_time instanceof \DateTime) {
            return clone $this->current_time;
        }
        else return new \DateTime();
    }

}