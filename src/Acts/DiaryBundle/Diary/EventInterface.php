<?php
namespace Acts\DiaryBundle\Diary;

interface EventInterface
{
    public function getName();

    public function getVenue();

    public function getStartTime();

    public function getEndTime();
}