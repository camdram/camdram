<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Diary\Diary;

/**
 * Class DiaryFactory
 *
 * A very basic service for creating Diary objects.
 *
 * @package Acts\DiaryBundle\Diary
 */
class DiaryFactory
{

    public function createDiary()
    {
        return new Diary;
    }

}
