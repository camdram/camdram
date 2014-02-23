<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;

class ExternalUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:external-users:generate')
            ->setDescription('Automatically create external users (Google, Raven etc) for users based on their registered e-mail address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Creating identities for users based on their email addresses</info>');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('ActsCamdramSecurityBundle:User')->findAll();

        foreach ($users as $user) {
            if (preg_match('/^[a-z]+[0-9]+$/i',$user->getEmail(), $matches)) {
                //Create identity for abc12 (crsid)
                $user->getEmail($user->getEmail().'@cam.ac.uk');
                $this->generateIdentity($user, 'raven', $matches[0], $output);
            }
            else if (preg_match('/^([a-z]+[0-9]+)@cam\.ac\.uk$/i', $user->getEmail(), $matches)) {
                //Create identity for abc12@cam.ac.uk
                $this->generateIdentity($user, 'raven', $matches[1], $output);
            }
            else if (preg_match('/^(.*)@(?:gmail|googlemail)\..*$/i', $user->getEmail(), $matches)) {
                //Create identity for xxx@gmail.com / xxx@googlemail.com
                $this->generateIdentity($user, 'google', $user->getEmail(), $output);
            }
        }
        $em->flush();

        $output->write("<info>Done</info>\r\n");
    }

    private function generateIdentity(User $user, $service, $username, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        if (is_null($user->getExternalUserByService($service))) {
            $e = new ExternalUser;
            $e->setService($service)
                ->setUser($user)
                ->setName($user->getName())
                ->setEmail($user->getEmail())
                ->setUsername($username);
            $user->addExternalUser($e);
            $em->persist($e);
            $output->writeln('Generated '.ucfirst($service).' external user for '.$user->getName());
        }
    }
}