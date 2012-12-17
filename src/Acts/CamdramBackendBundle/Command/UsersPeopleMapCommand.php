<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\UserIdentity;

class UsersPeopleMapCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:users:people-map')
            ->setDescription('Automatically match users to people based on their name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Mapping users to people</info>');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $utils = $this->getContainer()->get('camdram.security.name_utils');

        $users = $em->createQuery('SELECT u FROM ActsCamdramBundle:User u WHERE u.person_id IS NULL')->getResult();
        $count = 0;

        foreach ($users as $user) {
            $surname = $utils->extractSurname($user->getName());

            $people = $em->createQuery('SELECT p FROM ActsCamdramBundle:Person p WHERE p.name LIKE :name')
                ->setParameter('name', '% '.$surname.'%')->getResult();

            if ($person = $utils->getMostLikelyUser($user->getName(), $people)) {
                $this->handleLinkPerson($user, $person, $output);
                $count++;
            }
            if ($count >= 30) {
                $em->flush();
                $count = 0;
            }
        }
        $em->flush();
        $output->writeln('<info>Done</info>');
    }

    private function handleLinkPerson($user, $person, $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $utils = $this->getContainer()->get('camdram.security.name_utils');
        $score = $utils->getSimilarityScore($user->getName(), $person->getName());

        if ($score > 85) {
            //We're very certain that these are the same person
            $this->linkUserAndPerson($user, $person, $output);
        }
        elseif ($score > 70) {
            if ($dialog->askConfirmation($output,$this->buildQuestion($user, $person, $score))) {
                $this->linkUserAndPerson($user, $person, $output);
                $utils->registerEquivalence($user->getName(), $person->getName(), true);
            }
            else {
                $utils->registerEquivalence($user->getName(), $person->getName(), false);
            }
        }
    }

    private function buildQuestion($user, $person, $score) {
        $question = '<question>Link user "'.$user->getName().'" to person "'.$person->getName()
            .'" (similarity: '.$score.'/100) ';

        $question .= "\r\n    ".$user->getName().': last active in '
            .$user->getLogin()->format('Y');

        $question .= "\r\n    ".$person->getName().': ';
        foreach ($person->getRoles() as $role) {
            $show = $role->getShow();
            if ($show && $show->getDates()) {
                $question .= $role->getRole().' '.$show->getDates().', ';
            }
            elseif ($show && $show->getTimestamp()->format('U') > 0) {
                $question .= $role->getRole().' in '.$show->getTimestamp()->format('Y').', ';
            }
            else {
                $question .= $role->getRole().', ';
            }

        }

        $question .= "\r\n  ?  ";
        return $question;
    }

    private function linkUserAndPerson($user, $person, OutputInterface $output)
    {
        $user->setPerson($person);
        $output->writeln('Linked '.$user->getName().' -> '.$person->getName());
    }


}