<?php
namespace Acts\CamdramBackendBundle\DataFixtures\ORM;

use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\Yaml\Yaml;
use Acts\CamdramBundle\Entity\User;

class AccessControlEntryFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $e = new AccessControlEntry;
        $e->setUser($this->getReference('adminuser'));
        $e->setGrantedBy($this->getReference('testuser1'));
        $e->setEntityId('-1');
        $e->setCreatedAt(new \DateTime('2001-01-01'));
        $e->setType('security');
        $manager->persist($e);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }
}