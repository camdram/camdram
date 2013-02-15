<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Diary\Diary;

class DiaryFactory
{

    public function createDiary()
    {
        return new Diary;
    }

}