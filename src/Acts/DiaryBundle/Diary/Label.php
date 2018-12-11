<?php

namespace Acts\DiaryBundle\Diary;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class Label
 *
 * A label that appears at a certain point in the Diary. 'Week' labels are attached to a week; 'Period' labels
 * are attached to one or more weeks.
 */
class Label
{
    const TYPE_WEEK = 'week';
    const TYPE_PERIOD = 'period';

    /**
     * @var string The type of label, either LABEL_TYPE_WEEK or LABEL_TYPE_PERIOD
     * 
     * @Serializer\XmlElement(cdata=false)
     */
    private $type;

    /**
     * @var \DateTime The start time of the period this label refers to
     * 
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $start_at;

    /**
     * @var \DateTime The end time of the period this label refers to
     * 
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $end_at;

    /**
     * @var string The user-visible name of the label
     * 
     * @Serializer\XmlElement(cdata=false)
     */
    private $text;

    public function __construct($type, $text, \DateTime $start_at, \DateTime $end_at = null)
    {
        if ($type == self::TYPE_PERIOD && is_null($end_at)) {
            throw new \InvalidArgumentException('And end time must be specified for a period label');
        }

        $this->type = $type;
        $this->text = $text;
        $this->start_at = $start_at;
        $this->end_at = $end_at;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \DateTime $end_at
     */
    public function setEndAt($end_at)
    {
        $this->end_at = $end_at;
    }

    /**
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->end_at;
    }

    /**
     * @param \DateTime $start_at
     */
    public function setStartAt($start_at)
    {
        $this->start_at = $start_at;
    }

    /**
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->start_at;
    }
}
