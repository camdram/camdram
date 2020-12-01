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

    /**
     * {@inheritDoc}
     * @param \Doctrine\ORM\EntityManagerInterface $manager
     */
    public function load(ObjectManager $manager)
    {
        $first_key = min(array_keys(self::term_dates));
        for ($year = 1990; ; $year++) {
            list($lent_start, $lent_end) = $this->addTerm($manager, 'Lent', new \DateTime($year.'-01-16'), 0, 9);
            if (isset($michaelmas_end)) {
                $this->addVacation($manager, 'Christmas', $michaelmas_end, $lent_start);
            }

            list($easter_start, $easter_end) = $this->addTerm($manager, 'Easter', new \DateTime($year.'-04-24'), 0, 8);
            $this->addVacation($manager, 'Easter', $lent_end, $easter_start);

            if ($year >= $first_key) break;

            list($michaelmas_start, $michaelmas_end) = $this->addTerm($manager, 'Michaelmas', new \DateTime($year.'-10-06'), 0, 8);
            $this->addVacation($manager, 'Summer', $easter_end, $michaelmas_start);
        }

        for ($year = $first_key; array_key_exists($year, self::term_dates); $year++) {
            list($michaelmas_start, $michaelmas_end) = $this->addTerm($manager, 'Michaelmas',
                new \DateTime($year.'-10-'.self::term_dates[$year][0]), 0, 8);
            if (isset($easter_end)) {
                $this->addVacation($manager, 'Summer', $easter_end, $michaelmas_start);
            }

            list($lent_start, $lent_end) = $this->addTerm($manager, 'Lent',
                new \DateTime(($year + 1).'-01-'.self::term_dates[$year][1]), 0, 9);
            $this->addVacation($manager, 'Christmas', $michaelmas_end, $lent_start);

            list($easter_start, $easter_end) = $this->addTerm($manager, 'Easter',
                new \DateTime(($year + 1).'-04-'.self::term_dates[$year][2]), 0, 8);
            $this->addVacation($manager, 'Easter', $lent_end, $easter_start);
        }
    }

    private function rewindToSunday(\DateTime $date)
    {
        $date = clone $date;
        $day = $date->format('N');
        if ($day < 7) {
            $date->modify('-'.$day.' days');
        }

        return $date;
    }

    protected function addTerm(ObjectManager $manager, $name, \DateTime $latest_start_date, $start_week, $end_week)
    {
        $start_date = $this->rewindToSunday($latest_start_date);
        $start_date->modify('-'.$start_week.' weeks');
        $date = clone $start_date;
        for ($week = $start_week; $week <= $end_week; $week++) {
            $week_name = ($name == 'Easter' && $week == 8) ? 'May Week' : 'Week '.$week;

            $this->createWeek($manager, $week_name, $name.' '.$week_name, $date);
            $date->modify('+1 week');
        }
        $this->createPeriod($manager, $name, $name.' Term', $name.' Term '.$date->format('Y'), $start_date, $date);

        return array($start_date, $date);
    }

    protected function addVacation(ObjectManager $manager, $name, \DateTime $start, \DateTime $end)
    {
        $start_year = $start->format('Y');
        $end_year = $end->format('Y');
        $year = ($start_year == $end_year) ? $start_year : $start_year.'/'.$end_year;

        $this->createPeriod($manager, $name.' Vacation', $name.' Vacation', $name.' Vacation '.$year, $start, $end);
    }

    private function createPeriod(ObjectManager $manager, $short, $name, $long, $start, $end)
    {
        /** @var TimePeriodRepository $repo */
        $repo = $manager->getRepository(TimePeriod::class);
        $qb = $repo->createQueryBuilder('p');
        $query = $qb
            ->where($qb->expr()->andX('p.start_at < :end', 'p.end_at > :start'))
            ->setParameter('start', $start)->setParameter('end', $end)
            ->getQuery();
        $result = $query->getResult();

        if (count($result) == 0) {
            $p = new TimePeriod();
            $p->setShortName($short)->setName($name)->setFullName($long)
                ->setStartAt($start)->setEndAt($end);

            $manager->persist($p);
            $manager->flush();
        } else {
            $p = current($result);
        }

        $repo = $manager->getRepository(WeekName::class);
        $qb = $repo->createQueryBuilder('w');
        $query = $qb
            ->where($qb->expr()->andX('w.start_at >= :start', 'w.start_at < :end'))
            ->setParameter('start', $start)->setParameter('end', $end)
            ->getQuery();
        foreach ($query->getResult() as $week) {
            $p->addWeekName($week);
            $week->setTimePeriod($p);
        }
        $manager->flush();
    }

    private function createWeek(ObjectManager $manager, string $short_name, string $name, \DateTime $start): void
    {
        $repo = $manager->getRepository(WeekName::class);
        $qb = $repo->createQueryBuilder('w');
        $query = $qb->select('count(w.id) AS c')
            ->where($qb->expr()->andX('w.start_at = :start'))
            ->setParameter('start', $start)
            ->getQuery();
        $result = $query->getResult();
        $count = $result[0]['c'];
        if ($count == 0) {
            $w = new WeekName();
            $w->setShortName($short_name)->setName($name)
                ->setStartAt($start);

            $manager->persist($w);
            $manager->flush();
        }
    }
}
