<?php

namespace Acts\CamdramBundle\Entity;

/**
 * Week
 */
class Week
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $short_name;

    /**
     * @var \DateTime
     */
    private $start_at;

    /**
     * @var \DateTime
     */
    private $end_at;

    /**
     * Set name
     *
     * @param string $short_name
     *
     * @return Week
     */
    public function setShortName($short_name)
    {
        $this->short_name = $short_name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Week
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set start_at
     *
     * @param \DateTime $startAt
     *
     * @return Week
     */
    public function setStartAt($startAt)
    {
        $this->start_at = $startAt;

        return $this;
    }

    /**
     * Get start_at
     *
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->start_at;
    }

    /**
     * Set end_at
     *
     * @param \DateTime $endAt
     *
     * @return Week
     */
    public function setEndAt($endAt)
    {
        $this->end_at = $endAt;

        return $this;
    }

    /**
     * Get end_at
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->end_at;
    }
}
