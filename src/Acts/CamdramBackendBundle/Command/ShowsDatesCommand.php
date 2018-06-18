<?php

namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Acts\CamdramBundle\Entity\Show;
use Doctrine\ORM\EntityRepository;

class ShowsDatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:shows:dates')
            ->setDescription('Add an appropriate timestamp to each show, and link to a time period')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var $repo EntityRepository */
        $repo = $em->getRepository('ActsCamdramBundle:Show');

        $shows = $repo->findBy(array('start_at' => null));
        $i = 0;

        foreach ($shows as $show) {
            $start_date = null;
            $end_date = null;
            foreach ($show->getPerformances() as $performance) {
                if ($start_date == null || $start_date > $performance->getStartDate()) {
                    $start_date = $performance->getStartDate();
                }
                if ($end_date == null || $end_date < $performance->getEndDate()) {
                    $end_date = $performance->getEndDate();
                }
            }
            if ($start_date && $end_date) {
                $show->setStartAt($start_date);
                $show->setEndAt($end_date);
                $output->writeln('Set the dates of "'.$show->getName().'" to '.$start_date->format('jS F Y'));
                $i++;
                if ($i % 30 == 0) {
                    $em->flush();
                }
            }
        }
        $em->flush();
    }
}
