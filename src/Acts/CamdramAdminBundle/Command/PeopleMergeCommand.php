<?php

namespace Acts\CamdramAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Acts\CamdramBundle\Entity\Person;

class PeopleMergeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:people:merge')
            ->setDescription('Merge people with similar names into a single entry')
            ->addOption('similar', null, InputOption::VALUE_NONE, 'Whether to merge people by matching similar names')
            ->addOption('mapped', null, InputOption::VALUE_NONE, 'Whether to merge people explicitly mapped')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Linking people explicitly mapped using \'map_to\' and others with similar names</info>');

        if ($input->getOption('similar')) {
            $this->mergeSimilar($output);
        } elseif ($input->getOption('mapped')) {
            $this->mergeMapped($output);
        } else {
            $this->mergeMapped($output);
            $this->mergeSimilar($output);
        }

        $output->writeln('<info>Done</info>');
    }

    private function mergeSimilar(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $utils = $this->getContainer()->get('camdram.security.name_utils');
        $people_res = $em->getRepository('ActsCamdramBundle:Person');

        $em->getConnection()->exec('SET foreign_key_checks = 0');

        foreach ($people_res->findAll() as $person) {
            $surname = $utils->extractSurname($person->getName());
            $possibles = $people_res->createQueryBuilder('p')
                ->where('p.name LIKE :name')
                ->andWhere('p.id != :id')
                ->setParameter('name', '% '.$surname)
                ->setParameter('id', $person->getId())
                ->getQuery()->getResult();
            if (count($possibles) > 0) {
                $possible = $utils->getMostLikelyUser($person->getName(), $possibles, 70);
                if ($possible) {
                    //If the names are absolutely identical...assume there are two separate entries for a good reason
                    if ($possible->getName() == $person->getName()) {
                        continue;
                    }

                    $this->handleLinkPerson($person, $possible, $output);
                }
            }
        }
        $em->flush();

        $em->getConnection()->exec('SET foreign_key_checks = 1');
    }

    private function handleLinkPerson($p1, $p2, $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $utils = $this->getContainer()->get('camdram.security.name_utils');
        $score = $utils->getSimilarityScore($p1->getName(), $p2->getName());

        if ($score > 90) {
            //We're very certain that these are the same person
            $this->linkPeople($p1, $p2, $output);
        } elseif ($score > 70) {
            if ($dialog->askConfirmation($output, $this->buildQuestion($p1, $p2, $score))) {
                $this->linkPeople($p1, $p2, $output);
                $utils->registerEquivalence($p1->getName(), $p2->getName(), true);
            } else {
                $utils->registerEquivalence($p1->getName(), $p2->getName(), false);
            }
        }
    }

    private function buildQuestion($p1, $p2, $score)
    {
        $question = '<question>Merge people "'.$p1->getName().'" and "'.$p2->getName()
            .'" (similarity: '.$score.'/100) :';
        foreach (array($p1, $p2) as $person) {
            $question .= "\r\n    ".$person->getName().': ';
            foreach ($person->getRoles() as $role) {
                $show = $role->getShow();
                if ($show && $show->getDates()) {
                    $question .= $role->getRole().' '.$show->getDates().', ';
                } elseif ($show && $show->getTimestamp()->format('U') > 0) {
                    $question .= $role->getRole().' in '.$show->getTimestamp()->format('Y').', ';
                } else {
                    $question .= $role->getRole().', ';
                }
            }
            if (count($person->getRoles()) > 0) {
                $question = substr($question, 0, -2);
            }
        }
        $question .= "</question>\r\n?  ";

        return $question;
    }

    private function mergeMapped(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $people_res = $em->getRepository('ActsCamdramBundle:Person');
        foreach ($people_res->findAll() as $person) {
            if ($person->getMapTo()) {
                $other = $people_res->findOneById($person->getMapTo());
                $person->setMapTo(null);
                if (!$other) {
                    continue;
                }
                $this->linkPeople($person, $other, $output);
            }
        }
        $em->flush();
    }

    private function linkPeople(Person $p1, Person $p2, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $count1 = count($p1->getRoles());
        $count2 = count($p2->getRoles());
        if ($count1 < $count2) {
            $temp = $p2;
            $p2 = $p1;
            $p1 = $temp;
        }

        foreach ($p2->getRoles() as $role) {
            $role->setPerson($p1);
        }
        foreach ($p2->getUsers() as $u) {
            $u->setPerson($p1);
        }
        $output->writeln('Merged person '.$p2->getName().' into '.$p1->getName());

        $em->remove($p2);
        $em->flush();
    }
}
