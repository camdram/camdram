<?php
namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Week;
use Acts\CamdramBundle\Entity\WeekName;
use Doctrine\ORM\EntityManager;

class WeekManager
{
    private $weekRepository;
    private $periodRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->weekRepository = $entityManager->getRepository('ActsCamdramBundle:WeekName');
        $this->periodRepository = $entityManager->getRepository('ActsCamdramBundle:TimePeriod');
    }

    public function previousSunday(\DateTime $date)
    {
        //Rewind start date to previous Sunday at midnight
        $date = clone $date;
        $day = $date->format('N');
        if ($day < 7) $date->modify('-'.$day.' days');
        $date->setTime(0,0,0);
        return $date;
    }

    public function nextSunday(\DateTime $date)
    {
        //Move start date to next Sunday at midnight
        $date = clone $date;
        $day = $date->format('N');
        if ($day < 7) $date->modify('+'.(7-$day).' days');
        $date->setTime(0,0,0);
        return $date;
    }

    private function getWeekFromWeekName(WeekName $week_name)
    {
        $week = new Week();
        $week->setStartAt($week_name->getStartAt());
        $end_at = clone $week_name->getStartAt();
        $end_at->modify('+1 week');
        $week->setEndAt($end_at);
        $week->setShortName($week_name->getShortName());
        $week->setName($week_name->getName());
        return $week;
    }

    private function getWeekFromDate(\DateTime $date)
    {
        $date = clone $date;
        $week = new Week();
        if (($period = $this->periodRepository->findAt($date))) {
            $week->setName($period->getShortName());
            $week->setShortName('');
        }
        $week->setStartAt(clone $date);
        $date->modify('+1 week');
        $week->setEndAt($date);
        $week->setShortName('');
        return $week;
    }

    public function findBetween(\DateTime $start_date, \DateTime $end_date)
    {
        $weeks = array();

        foreach ($this->weekRepository->findBetween($start_date, $end_date) as $week_name) {
            $key = $week_name->getStartAt()->format('Y-m-d');
            $weeks[$key] = $this->getWeekFromWeekName($week_name);
        }

        $date = $this->previousSunday($start_date);
        while ($date <= $end_date) {
            $key = $date->format('Y-m-d');
            if (!isset($weeks[$key])) {
                $weeks[$key] = $this->getWeekFromDate($date);
            }
            $date->modify('+1 week');
        }
        ksort($weeks);
        return $weeks;
    }

    public function findAt(\DateTime $date)
    {
        $date = $this->previousSunday($date);
        if (($week_name = $this->weekRepository->findAt($date))) {
            return $this->getWeekFromWeekName($week_name);
        } else {
            return $this->getWeekFromDate($date);
        }

    }

}
