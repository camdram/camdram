<?php

namespace Acts\CamdramAdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UnauthorisedShowsEmailCommand
 *
 * This console command sends an e-mail for each show that is still unauthorised
 */
class UnauthorisedShowsEmailCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('camdram:unauthorised-shows:email')
            ->setDescription('Send e-mail to all those ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $unauthorised_shows = $this->entityManager->getRepository('ActsCamdramBundle:Show')->findUnauthorised();

        $moderation_manager = $this->getContainer()->get('acts.camdram.moderation_manager');

        foreach ($unauthorised_shows as $show) {
            $output->writeln('Sending authorisation reminder email for show "'.$show->getName().'"');
            $moderation_manager->emailEntityModerators($show);
        }
    }
}
