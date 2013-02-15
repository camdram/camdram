<?php
namespace Acts\DiaryBundle\Event;

class MultiDayEvent extends AbstractEvent implements MultiDayEventInterface
{
    private $start_date;

    private $end_date;

    private $exclude_date;

    public function getStartDate()
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTime $start_date)
    {
        $this->start_date = $start_date;
    }

    public function getEndDate()
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTime $end_date)
    {
        $this->end_date = $end_date;
    }

    public function getExcludeDate()
    {
        return $this->exclude_date;
    }

    public function setExcludeDate(\DateTime $exclude_date)
    {
        $this->exclude_date = $exclude_date;
    }
}