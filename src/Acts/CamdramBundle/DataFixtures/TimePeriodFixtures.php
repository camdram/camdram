<?php
namespace Acts\CamdramBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Acts\CamdramBundle\Entity\TimePeriod;
use Acts\CamdramBundle\Entity\TimePeriodRepository;
use Acts\CamdramBundle\Entity\Week;
use Acts\CamdramBundle\Entity\WeekName;

class TimePeriodFixtures extends Fixture
{
    /**
     * Start dates of Full Term, from the Statutes and Ordinances of the
     * University of Cambridge, 2019 Edition, Chapter II, Section 11,
     * https://www.admin.cam.ac.uk/univ/so/2019/chapter02-section11.html
     * Months are October, January, April. Array key is start of academic year.
     */
    private const term_dates = [
        2019 => [8, 14, 21],
        2020 => [6, 19, 27],
        2021 => [5, 18, 26],
        2022 => [4, 17, 25],
        2023 => [3, 16, 23],
        2024 => [8, 21, 29],
        2025 => [7, 20, 28],
        2026 => [6, 19, 27],
        2027 => [5, 18, 25],
        2028 => [3, 16, 24],
        2029 => [2, 15, 23],
    ];

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $em;

    /**
     * {@inheritDoc}
     * @param \Doctrine\ORM\EntityManagerInterface $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->em = $manager;
        $first_key = min(array_keys(self::term_dates));
        for ($year = 1990; ; $year++) {
            list($lent_start, $lent_end) = $this->addTerm('Lent', new \DateTime($year.'-01-16'), 0, 9);
            if (isset($michaelmas_end)) {
                $this->addVacation('Christmas', $michaelmas_end, $lent_start);
            }

            list($easter_start, $easter_end) = $this->addTerm('Easter', new \DateTime($year.'-04-24'), 0, 8);
            $this->addVacation('Easter', $lent_end, $easter_start);

            if ($year >= $first_key) break;

            list($michaelmas_start, $michaelmas_end) = $this->addTerm('Michaelmas', new \DateTime($year.'-10-06'), 0, 8);
            $this->addVacation('Summer', $easter_end, $michaelmas_start);
        }

        for ($year = $first_key; array_key_exists($year, self::term_dates); $year++) {
            list($michaelmas_start, $michaelmas_end) = $this->addTerm('Michaelmas',
                new \DateTime($year.'-10-'.self::term_dates[$year][0]), 0, 8);
            $this->addVacation('Summer', $easter_end, $michaelmas_start);

            list($lent_start, $lent_end) = $this->addTerm('Lent',
                new \DateTime(($year + 1).'-01-'.self::term_dates[$year][1]), 0, 9);
            $this->addVacation('Christmas', $michaelmas_end, $lent_start);

            list($easter_start, $easter_end) = $this->addTerm('Easter',
                new \DateTime(($year + 1).'-04-'.self::term_dates[$year][2]), 0, 8);
            $this->addVacation('Easter', $lent_end, $easter_start);
        }
        $this->em->flush();
    }

    private function rewindToSunday(\DateTime $date): \DateTime
    {
        $date = clone $date;
        $day = $date->format('N');
        if ($day < 7) {
            $date->modify('-'.$day.' days');
        }

        return $date;
    }

    /** @return array{\DateTime, \DateTime} */
    protected function addTerm(string $name, \DateTime $latest_start_date, int $start_week, int $end_week)
    {
        $start_date = $this->rewindToSunday($latest_start_date);
        $start_date->modify('-'.$start_week.' weeks');
        $date = clone $start_date;
        /** @var WeekName[] */
        $weeks = [];
        for ($week = $start_week; $week <= $end_week; $week++) {
            $week_name = ($name == 'Easter' && $week == 8) ? 'May Week' : 'Week '.$week;

            $weeks[] = $this->createWeek($week_name, $name.' '.$week_name, $date);
            $date->modify('+1 week');
        }
        $this->createPeriod($name, "$name Term", "$name Term ".$date->format('Y'), $start_date, $date, $weeks);

        return array($start_date, $date);
    }

    protected function addVacation(string $name, \DateTime $start, \DateTime $end): void
    {
        $start_year = $start->format('Y');
        $end_year = $end->format('Y');
        $year = ($start_year == $end_year) ? $start_year : $start_year.'/'.$end_year;

        $this->createPeriod("$name Vacation", "$name Vacation", "$name Vacation $year", $start, $end);
    }

    /** @param WeekName[] $weeks */
    private function createPeriod(string $short, string $name, string $long,
        \DateTime $start, \DateTime $end, array $weeks = []): void
    {
        $p = new TimePeriod();
        $p->setShortName($short)->setName($short)->setFullName($long)
            ->setStartAt($start)->setEndAt($end);
        $this->em->persist($p);

        foreach ($weeks as $week) {
            $p->addWeekName($week);
            $week->setTimePeriod($p);
        }
    }

    private function createWeek(string $short_name, string $name, \DateTime $start): WeekName
    {
        $w = new WeekName();
        $w->setShortName($short_name)->setName($name)
          ->setStartAt(clone $start);
        $this->em->persist($w);
        return $w;
    }
}
