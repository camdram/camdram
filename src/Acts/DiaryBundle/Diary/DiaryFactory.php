<?php

namespace Acts\DiaryBundle\Diary;

/**
 * Class DiaryFactory
 *
 * A very basic service for creating Diary objects.
 */
class DiaryFactory
{
    public function createDiary()
    {
        return new Diary();
    }
}
