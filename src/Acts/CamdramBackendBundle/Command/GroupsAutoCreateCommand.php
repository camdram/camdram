<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Acts\CamdramSecurityBundle\Entity\Group;

class GroupsAutoCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:groups:auto-create')
            ->setDescription('Create new groups based on societies and venues')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Generating new groups based on existing societies and venues</info>');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $accesses = $em->getRepository('ActsCamdramBundle:Access')->createQueryBuilder('a')
            ->where('a.type = :type')
            ->setParameter('type', 'society')
            ->getQuery()->getResult();
        ;

        $soc_rep = $em->getRepository('ActsCamdramBundle:Organisation');
        $user_rep = $em->getRepository('ActsCamdramBundle:User');
        $group_rep = $em->getRepository('ActsCamdramSecurityBundle:Group');
        $acl = array();

        foreach ($accesses as $access) {
            $soc = $soc_rep->findOneById($access->getRid());
            if (!isset($acl[$soc->getName()])) $acl[$soc->getName()] = array();
            if (!$access->getRevokeId()) $acl[$soc->getName()][] = $access->getUid();
        }

        foreach ($acl as $name => &$users) {
            if (count($users) == 0) unset($acl[$name]);
            sort($users);
        }

        foreach ($acl as $name => &$users) {
            foreach ($acl as $name2 => $users2) {
                if ($name != $name2 && $users == $users2) {
                    $output->writeln("De-duplicating $name and $name2");
                    unset($acl[$name]);
                }
            }
        }

        foreach ($acl as $name => $users) {
            if (!$group_rep->findOneByName($name)) {
                $g = new Group;
                $g->setName($name);
                foreach ($users as $uid) {
                    $u = $user_rep->findOneById($uid);
                    $g->addUser($u);
                }
                $em->persist($g);
                $output->writeln("Created group $name");
            }
        }
        $em->flush();

        $output->writeln('<info>Done</info>');
    }

}