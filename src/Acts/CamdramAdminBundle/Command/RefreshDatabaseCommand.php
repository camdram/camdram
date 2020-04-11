<?php

namespace Acts\CamdramAdminBundle\Command;

use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class RefreshDatabaseCommand extends Command
{
    private $conn;
    private $dev_warning;

    public function __construct(Connection $conn, $development_warning) {
        $this->conn = $conn;
        $this->dev_warning = $development_warning;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('camdram:database:refresh')
            ->setDescription('Refresh the database to latest schema and regenerate sample data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->conn->getDatabasePlatform()->getName() != 'sqlite'
            && !$this->dev_warning)
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
