<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class InitDbCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:init:db')
            ->setDescription('Initialise camdram database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('camdram:external-users:generate');
        $arguments = array('command' => 'camdram:external-users:generate');
        $command->run(new ArrayInput($arguments), $output);

        $command = $this->getApplication()->find('camdram:users:people-map');
        $arguments = array('command' => 'camdram:users:people-map');
        $command->run(new ArrayInput($arguments), $output);

        $command = $this->getApplication()->find('camdram:people:remove-redundant');
        $arguments = array('command' => 'camdram:people:remove-redundant');
        $command->run(new ArrayInput($arguments), $output);

        $command = $this->getApplication()->find('camdram:people:merge');
        $arguments = array('command' => 'camdram:people:merge');
        $command->run(new ArrayInput($arguments), $output);

        $command = $this->getApplication()->find('camdram:groups:auto-create');
        $arguments = array('command' => 'camdram:groups:auto-create');
        $command->run(new ArrayInput($arguments), $output);
    }
}
