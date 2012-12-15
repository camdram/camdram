<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Acts\CamdramSecurityBundle\Entity\Group;

class InitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:init')
            ->setDescription('Initialise a new instsallation of camdram')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}