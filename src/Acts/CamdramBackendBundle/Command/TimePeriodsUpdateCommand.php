<?php

namespace Acts\CamdramBackendBundle\Command;

use Acts\CamdramBundle\Entity\Week;
use Acts\CamdramBundle\Entity\WeekName;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Acts\CamdramBundle\Entity\TimePeriod;
use Acts\CamdramBundle\Entity\TimePeriodRepository;

class TimePeriodsUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:time-periods:update')
            ->setDescription('Automatically create time periods from the Computing Service\'s ical file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        for ($year = 1990; $year <= 2030; $year++) {
            list($lent_start, $lent_end) = $this->addTerm('Lent', new \DateTime($year.'-01-17'), 0, 9, $output);
            if (isset($michaelmas_end)) {
                $this->addVacation('Christmas', $michaelmas_end, $lent_start, $output);
            }

            list($easter_start, $easter_end) = $this->addTerm('Easter', new \DateTime($year.'-04-25'), 0, 8, $output);
            $this->addVacation('Easter', $lent_end, $easter_start, $output);

            list($michaelmas_start, $michaelmas_end) = $this->addTerm('Michaelmas', new \DateTime($year.'-10-07'), 0, 8, $output);
            $this->addVacation('Summer', $easter_end, $michaelmas_start, $output);
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

    protected function addTerm($name, \DateTime $latest_start_date, $start_week, $end_week, OutputInterface $output)
    {
        $start_date = $this->rewindToSunday($latest_start_date);
        $start_date->modify('-'.$start_week.' weeks');
        $date = clone $start_date;
        for ($week = $start_week; $week <= $end_week; $week++) {
            $week_name = ($name == 'Easter' && $week == 8) ? 'May Week' : 'Week '.$week;

            $this->createWeek($week_name, $name.' '.$week_name, $date, $output);
            $date->modify('+1 week');
        }
        $this->createPeriod($name, $name.' Term', $name.' Term '.$date->format('Y'), $start_date, $date, $output);

        return array($start_date, $date);
    }

    protected function addVacation($name, \DateTime $start, \DateTime $end, OutputInterface $output)
    {
        $start_year = $start->format('Y');
        $end_year = $end->format('Y');
        $year = ($start_year == $end_year) ? $start_year : $start_year.'/'.$end_year;

        $this->createPeriod($name.' Vacation', $name.' Vacation', $name.' Vacation '.$year, $start, $end, $output);
    }

    private function createPeriod($short, $name, $long, $start, $end, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var $repo TimePeriodRepository */
        $repo = $em->getRepository('ActsCamdramBundle:TimePeriod');
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

            $em->persist($p);
            $em->flush();
            $output->writeln('<info>Created time period '.$long.'</info>');
        } else {
            $p = current($result);
            $output->writeln('Time period '.$name. ' already exists');
        }

        $repo = $em->getRepository('ActsCamdramBundle:WeekName');
        $qb = $repo->createQueryBuilder('w');
        $query = $qb
            ->where($qb->expr()->andX('w.start_at >= :start', 'w.start_at < :end'))
            ->setParameter('start', $start)->setParameter('end', $end)
            ->getQuery();
        foreach ($query->getResult() as $week) {
            $p->addWeek($week);
            $week->setTimePeriod($p);
        }
        $em->flush();
    }

    private function createWeek($short_name, $name, $start, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var $repo TimePeriodRepository */
        $repo = $em->getRepository('ActsCamdramBundle:WeekName');
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

            $em->persist($w);
            $em->flush();
            $output->writeln('<info>Created week '.$name.'</info>');
        } else {
            $output->writeln('Week '.$name. ' already exists');
        }
    }
}
