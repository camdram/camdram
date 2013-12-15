<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class InitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:init')
            ->setDescription('Initialise a new installation of camdram')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('camdram:external-users:generate');
        $arguments = array('command' => 'camdram:external-users:generate');
        $command->run(new ArrayInput($arguments), $output);

        $command = $this->getApplication()->find('camdram:entities:slugs');
        $arguments = array('command' => 'camdram:entities:slugs');
        $command->run(new ArrayInput($arguments), $output);

        $command = $this->getApplication()->find('camdram:time-periods:update');
        $arguments = array('command' => 'camdram:time-periods:update');
        $command->run(new ArrayInput($arguments), $output);

        $command = $this->getApplication()->find('camdram:shows:dates');
        $arguments = array('command' => 'camdram:shows:dates');
        $command->run(new ArrayInput($arguments), $output);
    }
}
