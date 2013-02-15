<?php
namespace Acts\DiaryBundle\Event;

interface EventInterface
{
    public function getName();

    public function getVenue();

    public function getStartTime();

    public function getEndTime();

    public function getLink();

    public function getVenueLink();
}