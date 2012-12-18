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

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $entity_res = $em->getRepository('ActsCamdramBundle:Entity');

        $entities = $entity_res->findBySlug(null);
        $count = 0;
        foreach ($entities as $e) {
            $e->setSlug('');
            $output->writeln("Generated slug for ".$e->getName());
            $em->persist($e);
            $count++;
            if ($count == 100) {
                $em->flush();
                $count = 0;
            }
        }
        $em->flush();

        $output->write('<info>Done</info>');
    }
}