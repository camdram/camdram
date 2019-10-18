<?php

namespace Acts\CamdramAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class RefreshDatabaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:database:refresh')
            ->setDescription('Refresh the database to latest schema and regenerate sample data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conn = $this->getContainer()->get('doctrine.dbal.default_connection');
        if ($conn->getDatabasePlatform()->getName() != 'sqlite'
            && !$this->getContainer()->getParameter('env(DEVELOPMENT_WARNING)'))
        {
            //Precaution to avoid running this on the real database, as it drops the DB
            $output->writeln("camdram:database:refresh requires either a SQLite database or the development_warning flag to be set");
            return -1;
        }

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
}
