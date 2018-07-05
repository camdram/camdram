<?php
namespace Acts\CamdramBundle\Service;

class Time
{
    
    private static $mockDateTime;

    public static function mockDateTime(\DateTime $time)
    {
        self::$mockDateTime = $time;
    }

    public static function now()
    {
        return self::$mockDateTime ? self::$mockDateTime : new \DateTime;
    }
}