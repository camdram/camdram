<?php
namespace Acts\CamdramBackendBundle\Command;

use Acts\CamdramBundle\Entity\Person;
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
            ->leftJoin('p.roles', 'r')
            ->where('p.mapped_to is null')
            ->andWhere('r.id is null')
            ->getQuery();

        $people = $query->getResult();
        foreach ($people as $person) {
            $this->deletePerson($person, $output);
        }
        $em->flush();
    }

    private function deletePerson(Person $person, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $people_res = $em->getRepository('ActsCamdramBundle:Person');

        $mapped_people = $people_res->findBy(array('mapped_to' => $person));
        foreach ($mapped_people as $mapped_person) {
            $this->deletePerson($mapped_person, $output);
        }
        if (count($person->getUsers()) == 0 && count($person->getExternalUsers()) == 0) {
            $em->remove($person);
            $output->writeln('Deleted '.$person->getName());
        }
        else {
            $person->setMappedTo(null);
        }
    }
}
