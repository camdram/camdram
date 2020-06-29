<?php

namespace Acts\CamdramAdminBundle\Command;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Service\ModerationManager;
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
    private $moderationManager;

    public function __construct(EntityManagerInterface $entityManager, ModerationManager $mm)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->moderationManager = $mm;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('camdram:unauthorised-shows:email')
            ->setDescription('Send e-mail to all moderators of unauthorised shows.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $unauthorised_shows = $this->entityManager->getRepository(Show::class)->findUnauthorised();

        foreach ($unauthorised_shows as $show) {
            $output->writeln('Sending authorisation reminder email for show "'.$show->getName().'"');
            $this->moderationManager->emailEntityModerators($show);
        }
        return 0;
    }
}
