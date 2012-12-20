<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\Query\Expr;


class EntitiesSlugsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:entities:slugs')
            ->setDescription('Generate slugs for entities that do not have one')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Generating slugs for entities without one</info>');
        ini_set('memory_limit', '-1');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->getConfiguration()->setSQLLogger(null);

        $entity_res = $em->getRepository('ActsCamdramBundle:Entity');
        $entities = $entity_res->createQueryBuilder('e')
            ->where('e.slug is null')
            ->getQuery()->useQueryCache(false)->useResultCache(false)->iterate();

        $count = 0;
        foreach ($entities as $row) {
            $e = $row[0];

            $e->setSlug('');
            $output->writeln("Generated slug for ".$e->getName());
            $count++;
            if ($count % 100 == 0) {
                $output->writeln('Updating DB (memory usage: '.memory_get_usage(true).')');
                $em->flush();
                $em->clear();
            }
        }
        $em->flush();
        $em->clear();

        $output->write('<info>Done</info>');
    }
}