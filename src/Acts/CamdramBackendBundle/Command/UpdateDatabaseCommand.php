<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class UpdateDatabaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:database:update')
            ->setDescription('Update the database to latest schema and regenerate sample data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conn = $this->getContainer()->get('doctrine.dbal.default_connection');
        if ($conn->getDatabasePlatform()->getName() == 'sqlite') {
            $command = $this->getApplication()->find('doctrine:schema:drop');
            $arguments = array('command' => 'doctrine:schema:drop', '--force' => true);
            $command->run(new ArrayInput($arguments), $output);

            $command = $this->getApplication()->find('doctrine:schema:create');
            $arguments = array('command' => 'doctrine:schema:create');
            $command->run(new ArrayInput($arguments), $output);

            $command = $this->getApplication()->find('doctrine:fixtures:load');
            $arguments = array('command' => 'doctrine:fixtures:load', '--append' => true);
            $input = new ArrayInput($arguments);
            $input->setInteractive(false);
            $command->run($input, $output);
        }
        else {
            $command = $this->getApplication()->find('doctrine:migrations:migrate');
            $arguments = array('command' => 'doctrine:migrations:migrate');
            $command->run(new ArrayInput($arguments), $output);
        }
    }
}
