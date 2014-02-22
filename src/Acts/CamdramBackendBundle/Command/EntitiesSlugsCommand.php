<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gedmo\Sluggable\Util as Sluggable;

use Doctrine\ORM\Query\Expr;

/**
 * Class EntitiesSlugsCommand
 *
 * Generates slugs for any entity with a blank slug
 *
 * @package Acts\CamdramBackendBundle\Command
 */

class EntitiesSlugsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:entities:slugs')
            ->setDescription('Generate slugs for entities that do not have one')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Whether to regenerate all slugs')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Generating slugs for entities without one</info>');

        $search_all = (bool) $input->getOption('all');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->getConfiguration()->setSQLLogger(null);

        $qb = $em->getRepository('ActsCamdramBundle:Show')->createQueryBuilder('s');
        if (!$search_all) $qb->where('s.slug is null');
        $shows = $qb->getQuery()->useQueryCache(false)->useResultCache(false)->iterate();
        $this->createSlugs($shows, $output);
        $em->clear();

        $qb = $em->getRepository('ActsCamdramBundle:Organisation')->createQueryBuilder('o');
        if (!$search_all) $qb->where('o.slug is null');
        $orgs = $qb->getQuery()->useQueryCache(false)->useResultCache(false)->iterate();
        $this->createSlugs($orgs, $output);
        $em->clear();

        $qb = $em->getRepository('ActsCamdramBundle:Person')->createQueryBuilder('p');
        if (!$search_all) $qb->where('p.slug is null');
        $people = $qb->getQuery()->useQueryCache(false)->useResultCache(false)->iterate();
        $this->createSlugs($people, $output);
        $em->clear();

        $output->write('<info>Done</info>');
    }

    protected function createSlugs($entities, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $count = 0;
        foreach ($entities as $row) {
            $e = $row[0];

            $e->setSlug('__id__');

            $count++;
            if ($count % 100 == 0) {
                $output->writeln('Updating DB (memory usage: '.memory_get_usage(true).')');
                $em->flush();
                $em->clear();
            }
            $output->writeln("Generated slug for ".$e->getName());
        }
        $em->flush();
        $em->clear();
    }
}