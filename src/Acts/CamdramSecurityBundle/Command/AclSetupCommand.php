<?php
namespace Acts\CamdramSecurityBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

use Acts\CamdramSecurityBundle\Security\Acl\Permission\MaskBuilder;

class AclSetupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:acl:setup')
            ->setDescription('Set up basic ACL rules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $adminIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $loggedInIdentity = new RoleSecurityIdentity(' IS_AUTHENTICATED_FULLY');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $group = $em->getRepository('ActsCamdramSecurityBundle:Group')->findOneBy(array('short_name' => 'adc'));
        $adcIdentity = new RoleSecurityIdentity('GROUP_adc');

        $venue = $em->getRepository('ActsCamdramBundle:Venue')->findOneBy(array('name' => 'ADC Theatre'));

        $this->grantObject($adcIdentity, $venue, $output);

        $this->grantClass($adminIdentity, 'Acts\\CamdramBundle\\Entity\\Society', $output);
        $this->grantClass($adminIdentity, 'Acts\\CamdramBundle\\Entity\\Venue', $output);
        $this->grantClass($adminIdentity, 'Acts\\CamdramBundle\\Entity\\Show', $output);
        $this->grantClass($adminIdentity, 'Acts\\CamdramBundle\\Entity\\Person', $output);
        $this->grantClass($adminIdentity, 'Acts\\CamdramBundle\\Entity\\User', $output);
        $this->grantClass($adminIdentity, 'Acts\\CamdramSecurityBundle\\Entity\\Group', $output);

        $this->grantClass($loggedInIdentity, 'Acts\\CamdramBundle\\Entity\\Show', $output, MaskBuilder::MASK_CREATE);


    }

    private function grantClass(SecurityIdentityInterface $identity, $class, OutputInterface $output, $level = MaskBuilder::MASK_OWNER)
    {
        /** @var $aclProvider \Symfony\Component\Security\Acl\Dbal\MutableAclProvider */
        $aclProvider = $this->getContainer()->get('security.acl.provider');

        $objectIdentity = new ObjectIdentity('class', $class);
        try {
            $acl = $aclProvider->createAcl($objectIdentity);
        }
        catch (AclAlreadyExistsException $e) {
            $acl = $aclProvider->findAcl($objectIdentity);
        }

        $found = false;
        foreach ($acl->getClassAces() as $classAce) {
            if ($classAce->getSecurityIdentity() == $identity) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $acl->insertClassAce($identity, $level);
            $aclProvider->updateAcl($acl);
            $output->writeln('Granted '.$class.' to '.$identity);
        }
    }

    private function grantObject(SecurityIdentityInterface $identity, $object, OutputInterface $output, $level = MaskBuilder::MASK_OWNER)
    {
        /** @var $aclProvider \Symfony\Component\Security\Acl\Dbal\MutableAclProvider */
        $aclProvider = $this->getContainer()->get('security.acl.provider');

        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        try {
            $acl = $aclProvider->createAcl($objectIdentity);
        }
        catch (AclAlreadyExistsException $e) {
            $acl = $aclProvider->findAcl($objectIdentity);
        }

        $found = false;
        foreach ($acl->getClassAces() as $classAce) {

            if ($classAce->getSecurityIdentity() == $identity) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $acl->insertObjectAce($identity, $level);
            $aclProvider->updateAcl($acl);
            $output->writeln('Granted '.$object.' to '.$identity);
        }
    }

}