<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Sabre\VObject\Reader;
use Acts\CamdramBundle\Entity\TimePeriod;
use Acts\CamdramBundle\Entity\TimePeriodGroup;
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
        $output->writeln('Creating time periods from existing term dates');
        $this->createFromTermDates($output);

        $output->writeln('Fetching ical file...');
        list($terms, $min, $max) = $this->getCalendarData();
        $output->writeln('Done');
        $last_date = null;
        $holidays = array(
            'Lent' => 'Christmas',
            'Easter' => 'Easter',
            'Michaelmas' => 'Summer',
        );

        for ($year=$min; $year <= $max; $year++) {
            foreach (array('Lent', 'Easter', 'Michaelmas') as $term_name) {
                if (!isset($terms[$term_name.' term '.$year])) continue;
                $term = $terms[$term_name.' term '.$year];

                $term_start = $term['start'];
                //Move start back to the previous Sunday
                $startday = $term_start->format('N');
                if ($startday < 7) $term_start->modify('-'.$startday.' days');

                //Create holiday
                if (!is_null($last_date)) {
                    $holiday_name = $holidays[$term_name];
                    $holiday_year = $last_date->format('Y');
                    if ($last_date->format('Y') != $term_start->format('Y')) $holiday_year .= '/'.$term_start->format('Y');
                    $this->createTerm($holiday_name, $holiday_name.' Vacation', $holiday_name.' Vacation '.$holiday_year,
                            $last_date, $term_start, $output, true);
                }

                $num = 0;
                $date = clone $term_start;

                if ($term_name == 'Lent') $term['end']->modify('+1 week');

                while ($date < $term['end']) {
                    $start = clone $date;
                    $date->modify('+1 week');
                    $end = clone $date;
                    $this->createTerm('Week '.$num, $term_name.' Week '.$num, $term_name.' Term '.$year.' Week '.$num,
                            $start, $end, $output);
                    $num++;
                }
                $last_date = clone $date;

                //Create group
                $this->createGroup($term_name. ' Term', $term_name.' Term '.$year, $term_start, $date, $output);
            }
        }
    }

    private function createGroup($name, $long, $start, $end, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var $repo TimePeriodRepository */
        $repo = $em->getRepository('ActsCamdramBundle:TimePeriodGroup');
        $qb = $repo->createQueryBuilder('g');
        $query = $qb
            ->where($qb->expr()->andX('g.start_at < :start', 'g.end_at > :start'))
            ->orWhere($qb->expr()->andX('g.start_at < :end', 'g.end_at > :end'))
            ->orWhere($qb->expr()->andX('g.start_at = :start', 'g.end_at = :end'))
            ->setParameter('start', $start)->setParameter('end', $end)
            ->getQuery();
        $result = $query->getOneOrNullResult();


        if (!$result) {
            $g = new TimePeriodGroup;
            $g->setName($name)->setLongName($long)
                ->setStartAt($start)->setEndAt($end);

            $em->persist($g);
            $em->flush();
            $output->writeln('<info>Created time period group '.$long.'</info>');
        }
        else {
            $g = $result;
            $output->writeln('Time period group '.$long. ' already exists');
        }

        /** @var $repo TimePeriodRepository */
        $repo = $em->getRepository('ActsCamdramBundle:TimePeriod');
        $qb = $repo->createQueryBuilder('p');
        $query = $qb
            ->where($qb->expr()->andX('p.start_at >= :start', 'p.end_at <= :end'))
            ->setParameter('start', $start)->setParameter('end', $end)
            ->getQuery();
        foreach ($query->getResult() as $period) {
            $g->addPeriod($period);
            $period->setGroup($g);
        }
        $em->flush();
    }

    private function createTerm($short, $name, $long, $start, $end, OutputInterface $output, $holiday = false)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var $repo TimePeriodRepository */
        $repo = $em->getRepository('ActsCamdramBundle:TimePeriod');
        $qb = $repo->createQueryBuilder('p');
        $query = $qb->select('count(p.id) AS c')
            ->where($qb->expr()->andX('p.start_at < :start', 'p.end_at > :start'))
            ->orWhere($qb->expr()->andX('p.start_at < :end', 'p.end_at > :end'))
            ->orWhere($qb->expr()->andX('p.start_at = :start', 'p.end_at = :end'))
            ->setParameter('start', $start)->setParameter('end', $end)
            ->getQuery();
        $result = $query->getResult();
        $count = $result[0]['c'];
        if ($count == 0) {
            $p = new TimePeriod;
            $p->setShortName($short)->setName($name)->setLongName($long)
                ->setStartAt($start)->setEndAt($end);

            $em->persist($p);
            $em->flush();
            $output->writeln('<info>Created time period '.$long.'</info>');
        }
        else {
            $output->writeln('Time period '.$long. ' already exists');
        }
    }

    private function getCalendarData()
    {
        $url = $this->getContainer()->getParameter('term_dates_ical');
        $data = file_get_contents($url);
        $calendar = Reader::read($data);

        $terms = array();
        $min_year = 9999;
        $max_year = 0;

        foreach($calendar->VEVENT as $event) {
            $summary = $event->SUMMARY->value;
            if (preg_match('/^Full (.*)$/i', $summary, $matches)) {
                $start = $event->DTSTART->getDateTime();
                $end = $event->DTEND->getDateTime();
                $year = $start->format('Y');
                $full_name = $matches[1].' '.$year;
                if ($year < $min_year) $min_year = $year;
                if ($year > $max_year) $max_year = $year;

                if (isset($terms[$full_name])) {
                    if ($start > $terms[$full_name]['end']) {
                        $terms[$full_name]['end'] = $end;
                    }
                    elseif ($end < $terms[$full_name]['start']) {
                        $terms[$full_name]['start'] = $start;
                    }
                }
                else {
                    $terms[$full_name] = array('start' => $start, 'end' => $end);
                }
            }

        }
        return array($terms, $min_year, $max_year);
    }

    private function createFromTermDates(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $term_repo = $em->getRepository('ActsCamdramBundle:TermDate');
        $term_dates = $term_repo->findAll();
        $last_date = null;
        $last_vaction = null;

        foreach ($term_dates as $term_date) {
            $parts = explode(' ',$term_date->getName());
            $term = $parts[0];
            if ($term == 'Michaelmas' || $term == 'Lent' || $term == 'Easter') {
                $year = $term_date->getStartDate()->format('Y');
                $start = $term_date->getStartDate()->modify('-1 day');
                $end = $term_date->getEndDate()->modify('-1 day');

                if ($last_date != null) {
                    $holiday_name = $last_vacation;
                    $parts = explode(' ', $holiday_name);
                    $holiday_name = $parts[0];
                    $holiday_year = $parts[2];

                    $this->createTerm($holiday_name, $holiday_name.' Vacation', $holiday_name.' Vacation '.$holiday_year,
                        $last_date, $start, $output, true);
                }

                $date = clone $start;
                for ($num = $term_date->getFirstWeek(); $num <= $term_date->getLastWeek(); $num++) {
                    $start = clone $date;
                    $date->modify('+1 week');
                    $end = clone $date;
                    $this->createTerm('Week '.$num, $term.' Week '.$num, $term.' Term '.$year.' Week '.$num,
                        $start, $end, $output);
                }

                $last_date = clone $date;
                $last_vacation = $term_date->getVacation();
                $this->createGroup($term. ' Term', $term.' Term '.$year, $start, $end, $output);
            }
        }
    }

}