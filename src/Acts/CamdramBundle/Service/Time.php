<?php
namespace Acts\CamdramBundle\Service;

class Time
{
    /** @var ?\DateTime */
    private static $mockDateTime;

    public static function mockDateTime(\DateTime $time): void
    {
        self::$mockDateTime = $time;
    }

    public static function now(): \DateTime
    {
        return self::$mockDateTime ? self::$mockDateTime : new \DateTime;
    }
}
