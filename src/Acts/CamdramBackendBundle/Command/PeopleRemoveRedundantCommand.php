<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\Query\Expr;

class PeopleRemoveRedundantCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:people:remove-redundant')
            ->setDescription('Remove people no no associated user or role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Removing people with no associated user or role on a show</info>');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $people_res = $em->getRepository('ActsCamdramBundle:Person');
        $query = $people_res->createQueryBuilder('p')
            ->leftJoin('ActsCamdramBundle:Role', 'r', Expr\Join::WITH, 'p.id = r.person_id')
            ->where('r.id is null')
            ->getQuery();
        $people = $query->getResult();
        foreach ($people as $p) {
            $em->remove($p);
            echo 'Deleted '.$p->getName()."\r\n";
        }
        $em->flush();
    }
}
