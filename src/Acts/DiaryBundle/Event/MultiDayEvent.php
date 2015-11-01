<?php

namespace Acts\DiaryBundle\Event;

class MultiDayEvent extends AbstractEvent implements MultiDayEventInterface
{
    /**
     * @var \DateTime
     */
    private $start_date;

    /**
     * @var \DateTime
     */
    private $end_date;

    /**
     * @var \DateTime
     */
    private $exclude_date;

    /**
     * {@inheritdoc}
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set the first date on which the event takes place
     *
     * @param \DateTime $start_date
     */
    public function setStartDate(\DateTime $start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set the last date on which the event takes place
     *
     * @param \DateTime $end_date
     */
    public function setEndDate(\DateTime $end_date)
    {
        $this->end_date = $end_date;
    }

    /**
     * {@inheritdoc}
     */
    public function getExcludeDate()
    {
        return $this->exclude_date;
    }

    /**
     * Set a single date between the start and end dates on which the event does not take place
     *
     * @param \DateTime|null $exclude_date
     */
    public function setExcludeDate($exclude_date)
    {
        $this->exclude_date = $exclude_date;
    }
}
