<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\UserIdentity;

class UsersIdentitiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:users:identities')
            ->setDescription('Automatically create identities (Google, Raven etc) for users based on their registered e-mail address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Creating identities for users based on their email addresses</info>');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('ActsCamdramBundle:User')->findAll();

        foreach ($users as $user) {
            if (preg_match('/^[a-z]+[0-9]+$/i',$user->getEmail())) {
                //Create identity for abc12 (crsid)
                $this->generateIdentity($user, 'raven', $output);
            }
            else if (preg_match('/^([a-z]+[0-9]+)@cam\.ac\.uk$/i', $user->getEmail(), $matches)) {
                //Create identity for abc12@cam.ac.uk
                $user->setEmail($matches[1]);
                $this->generateIdentity($user, 'raven', $output);
            }
            else if (preg_match('/^(.*)@cantab.net$/i', $user->getEmail(), $matches)) {
                //Create identity for fred.smith@cantab.net
                $this->generateIdentity($user, 'cantab', $output);
            }
            else if (preg_match('/^(.*)@(?:gmail|googlemail)\..*$/i', $user->getEmail(), $matches)) {
                //Create identity for xxx@gmail.com / xxx@googlemail.com
                $this->generateIdentity($user, 'google', $output);
            }
            else if (preg_match('/^(.*)@(?:hotmail|live|outlook|msn)\..*$/i', $user->getEmail(), $matches)) {
                //Create identity for xxx@hotmail.com etc
                $this->generateIdentity($user, 'hotmail', $output);
            }
            else if (preg_match('/^(.*)@(?:yahoo)\..*$/i', $user->getEmail(), $matches)) {
                //Create identity for xxx@yahoo.com etc
                $this->generateIdentity($user, 'yahoo', $output);
            }
        }
        $em->flush();

        $output->write('<info>Done</info>');
    }

    private function generateIdentity(User $user, $service, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        if (is_null($user->getIdentityByServiceName($service))) {
            $i = new UserIdentity;
            $i->setService($service);
            $i->setRemoteUser($user->getEmail());
            $i->setUser($user);
            $user->addIdentity($i);
            $em->persist($i);
            $output->writeln('Generated '.ucfirst($service).' identity for '.$user->getName());
        }
    }
}