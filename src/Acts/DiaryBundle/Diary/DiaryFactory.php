<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Diary\Diary;

class DiaryFactory
{

    public static function create()
    {
        return new Diary;
    }

}