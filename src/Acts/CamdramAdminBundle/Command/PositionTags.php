<?php

namespace Acts\CamdramAdminBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Doctrine\ORM\EntityManagerInterface;

use Acts\CamdramBundle\DataFixtures\PositionFixtures;
use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramBundle\EventListener\AdvertListener;
use Acts\CamdramBundle\EventListener\RoleListener;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;

class PositionTags extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AdvertListener
     */
    private $advertListener;

    /**
     * @var RoleListener
     */
    private $roleListener;

    public function __construct(EntityManagerInterface $entityManager,
            AdvertListener $advertListener, RoleListener $roleListener)
    {
        $this->entityManager = $entityManager;
        $this->advertListener = $advertListener;
        $this->roleListener = $roleListener;

        parent::__construct();
    }

    protected static $defaultName = 'camdram:position-tags';

    protected function configure(): void
    {
        $this
            ->setDescription('Refresh position definitions, and all advert and role position links')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Rebuilding positions table...');
        // Clear the existing tags and positions.
        $this->entityManager->createQuery(
            'DELETE FROM \Acts\CamdramBundle\Entity\Position p')->execute();
        PositionFixtures::loadStatic($this->entityManager);

        $output->writeln('Updating adverts...');
        $advertRepository = $this->entityManager->getRepository(Advert::class);
        foreach ($advertRepository->findAll() as $advert) {
            $this->advertListener->updatePositions($advert);
        }
        $this->entityManager->flush();

        $output->writeln('Updating roles...');
        $roleRepository = $this->entityManager->getRepository(Role::class);
        foreach ($roleRepository->findAll() as $role) {
            $this->roleListener->updatePosition($role);
        }
        $this->entityManager->flush();

        return 0;
    }

}
