<?php

namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\TimePeriod;
use Acts\CamdramBundle\Entity\Week;
use Acts\CamdramBundle\Entity\WeekName;
use Doctrine\ORM\EntityManagerInterface;

class WeekManager
{
    /** @var \Acts\CamdramBundle\Entity\WeekNameRepository */
    private $weekRepository;
    /** @var \Acts\CamdramBundle\Entity\TimePeriodRepository */
    private $periodRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->weekRepository = $entityManager->getRepository(WeekName::class);
        $this->periodRepository = $entityManager->getRepository(TimePeriod::class);
    }

    public function previousSunday(\DateTime $date): \DateTime
    {
        //Rewind start date to previous Sunday at midnight
        $date = clone $date;
        $day = $date->format('N');
        if ($day < 7) {
            $date->modify('-'.$day.' days');
        }
        $date->setTime(0, 0, 0);

        return $date;
    }

    public function nextSunday(\DateTime $date): \DateTime
    {
        //Move start date to next Sunday at midnight
        $date = clone $date;
        $day = (int)($date->format('N'));
        if ($day < 7) {
            $date->modify('+'.(7 - $day).' days');
        }
        $date->setTime(0, 0, 0);

        return $date;
    }

    /** @return array<string,Week> */
    public function findBetween(\DateTime $start_date, \DateTime $end_date): array
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

    public function findAt(\DateTime $date): Week
    {
        $date = $this->previousSunday($date);
        if (($week_name = $this->weekRepository->findAt($date))) {
            return $this->getWeekFromWeekName($week_name);
        } else {
            return $this->getWeekFromDate($date);
        }
    }

    /**
     * Given any array of performances return a string version
     * of the performances datses in "Cambridge terms" e.g.
     * "Lent Week 8 to Week 9".
     *
     * This function is used when advertising show vacancies, for
     * example.
     */
    public function getPerformancesWeeksAsString(\DateTime $startAt, \DateTime $endAt): ?string
    {
        $res = "";
        $start_week = $this->findAt($startAt);
        $end_week   = $this->findAt($endAt);
        if ($start_week->getName() == $end_week->getName()) {
            /* Any show that runs for less than a week, e.g. most
             * shows at the ADC Theatre.
             */
            $res = $start_week->getName();
        } else {
            /* Less common, perhaps a two week run. */
            $res = $start_week->getName() . " to ";
            if (explode(' ', $start_week->getName())[0] == explode(' ', $end_week->getName())[0]) {
                /* Both weeks are in the same term. */
                $res = $res . $end_week->getShortName();
            } else {
                /* The show spans multiple terms. */
                $res = $res . $end_week->getName();
            }
        }

        return $res;
    }

    private function getWeekFromWeekName(WeekName $week_name): Week
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

    private function getWeekFromDate(\DateTime $date): Week
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
}
