<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class JsRoutingDumpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:js-routing:dump')
            ->setDescription('Initialise a new installation of camdram')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $this->getContainer()->get('kernel')->getEnvironment();
        $target = $this->getContainer()->get('kernel')->getRootDir().'/../web/js/fos_js_routes.'.$env.'.js';

        $command = $this->getApplication()->find('fos:js-routing:dump');
        $arguments = array('command' => 'fos:js-routing:dump', '--target' => $target);
        $command->run(new ArrayInput($arguments), $output);
    }
}
