<?php
namespace Acts\CamdramBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Acts\CamdramBundle\Entity\TimePeriod;
use Acts\CamdramBundle\Entity\Week;
use Acts\CamdramBundle\Entity\WeekName;

class TimePeriodFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($year = 1990; $year <= 2030; $year++) {
            list($lent_start, $lent_end) = $this->addTerm($manager, 'Lent', new \DateTime($year.'-01-16'), 0, 9);
            if (isset($michaelmas_end)) {
                $this->addVacation($manager, 'Christmas', $michaelmas_end, $lent_start);
            }

            list($easter_start, $easter_end) = $this->addTerm($manager, 'Easter', new \DateTime($year.'-04-24'), 0, 8);
            $this->addVacation($manager, 'Easter', $lent_end, $easter_start);

            list($michaelmas_start, $michaelmas_end) = $this->addTerm($manager, 'Michaelmas', new \DateTime($year.'-10-06'), 0, 8);
            $this->addVacation($manager, 'Summer', $easter_end, $michaelmas_start);
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
        /** @var $repo TimePeriodRepository */
        $repo = $manager->getRepository('ActsCamdramBundle:TimePeriod');
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

        $repo = $manager->getRepository('ActsCamdramBundle:WeekName');
        $qb = $repo->createQueryBuilder('w');
        $query = $qb
            ->where($qb->expr()->andX('w.start_at >= :start', 'w.start_at < :end'))
            ->setParameter('start', $start)->setParameter('end', $end)
            ->getQuery();
        foreach ($query->getResult() as $week) {
            $p->addWeek($week);
            $week->setTimePeriod($p);
        }
        $manager->flush();
    }

    private function createWeek(ObjectManager $manager, $short_name, $name, $start)
    {
        /** @var $repo TimePeriodRepository */
        $repo = $manager->getRepository('ActsCamdramBundle:WeekName');
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