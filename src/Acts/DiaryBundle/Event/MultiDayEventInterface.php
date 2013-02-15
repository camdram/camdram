<?php
namespace Acts\DiaryBundle\Event;

interface MultiDayEventInterface extends EventInterface
{
    /**
     * @return \DateTime
     */
    public function getExcludeDate();

    /**
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * @return \DateTime
     */
    public function getEndDate();
}