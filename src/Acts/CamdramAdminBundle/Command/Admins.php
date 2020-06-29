<?php

namespace Acts\CamdramAdminBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Doctrine\ORM\EntityManagerInterface;

use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;

class Admins extends Command
{

    /**
     * @var AclProvider
     */
    private $aclProvider;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(AclProvider $aclProvider, EntityManagerInterface $entityManager)
    {
        $this->aclProvider = $aclProvider;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected static $defaultName = 'camdram:admins';

    protected function configure()
    {
        $this
            ->setDescription('List all current Camdram admins')
            ->addOption('grant', null,
                    InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                    'Email or ID of user to grant admin rights')
            ->addOption('revoke', null,
                    InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                    'Email or ID of user to revoke admin rights')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($input->getOption('grant') as $grant) {
            $this->grantAccess($grant);
        }
        foreach ($input->getOption('revoke') as $revoke) {
            $this->revokeAccess($revoke);
        }

        $table = new Table($output);
        $table->setHeaders(array('ID', 'Name', 'Email', 'Type', 'Last Login'));

        $admins = $this->aclProvider->getAdmins(-10);
        foreach ($admins as $admin) {
            $loginDate = $admin->getLastLoginAt() ? $admin->getLastLoginAt()->format('Y-m-d') : '';
            $table->addRow([
                $admin->getId(),
                $admin->getName(),
                $admin->getEmail(),
                $this->getAdminType($admin),
                $loginDate
            ]);
        }

        $table->render();
        return 0;
    }

    private function grantAccess($idOrEmail)
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        if (is_numeric($idOrEmail)) {
            $user = $userRepo->findOneById($idOrEmail);
        }
        else {
            $user = $userRepo->findOneByEmail($idOrEmail);
        }
        if (!$user) {
            throw new \RuntimeException("User $idOrEmail cannot be found");
        }

        $this->aclProvider->grantAdmin($user);
    }

    private function revokeAccess($idOrEmail)
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        if (is_numeric($idOrEmail)) {
            $user = $userRepo->findOneById($idOrEmail);
        }
        else {
            $user = $userRepo->findOneByEmail($idOrEmail);
        }
        if (!$user) {
            throw new \RuntimeException("User $idOrEmail cannot be found");
        }

        $this->aclProvider->revokeAdmin($user);
    }

    private function getAdminType(User $user)
    {
        $roles = $user->getRoles();
        foreach (['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR'] as $role) {
            if (in_array($role, $roles)) {
                return $role;
            }
        }
        return '???';
    }

}
