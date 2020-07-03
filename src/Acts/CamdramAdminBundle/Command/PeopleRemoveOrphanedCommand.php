<?php

namespace Acts\CamdramAdminBundle\Command;

use Acts\CamdramBundle\Entity\Person;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class PeopleRemoveOrphanedCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('camdram:people:remove-orphaned')
            ->setDescription('Remove people no no associated user or role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Removing people with no associated user or role on a show</info>');

        $people_res = $this->entityManager->getRepository(Person::class);
        $query = $people_res->createQueryBuilder('p')
            ->leftJoin('p.roles', 'r')
            ->where('p.mapped_to is null')
            ->andWhere('r.id is null')
            ->getQuery();

        $people = $query->getResult();
        foreach ($people as $person) {
            $this->deletePerson($person, $output);
        }
        $this->entityManager->flush();
        return 0;
    }

    private function deletePerson(Person $person, OutputInterface $output)
    {
        $people_res = $this->entityManager->getRepository(Person::class);

        $mapped_people = $people_res->findBy(array('mapped_to' => $person));
        foreach ($mapped_people as $mapped_person) {
            $this->deletePerson($mapped_person, $output);
        }
        if (count($person->getUsers()) == 0) {
            $this->entityManager->remove($person);
            $output->writeln('Deleted '.$person->getName());
        } else {
            $person->setMappedTo(null);
        }
    }
}
