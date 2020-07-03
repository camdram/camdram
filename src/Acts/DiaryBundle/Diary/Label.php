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
     * @var ?\DateTime The end time of the period this label refers to
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

    public function __construct(string $type, string $text, \DateTime $start_at, \DateTime $end_at = null)
    {
        if ($type == self::TYPE_PERIOD && is_null($end_at)) {
            throw new \InvalidArgumentException('And end time must be specified for a period label');
        }

        $this->type = $type;
        $this->text = $text;
        $this->start_at = $start_at;
        $this->end_at = $end_at;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setEndAt(?\DateTime $end_at): void
    {
        $this->end_at = $end_at;
    }

    public function getEndAt(): ?\DateTime
    {
        return $this->end_at;
    }

    public function setStartAt(\DateTime $start_at): void
    {
        $this->start_at = $start_at;
    }

    public function getStartAt(): \DateTime
    {
        return $this->start_at;
    }
}
