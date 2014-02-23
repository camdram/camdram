<?php
namespace Acts\CamdramBackendBundle\DataFixtures\ORM;

use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\Yaml\Yaml;
use Acts\CamdramSecurityBundle\Entity\User;

class AccessControlEntryFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        //Make the admin user an admin
        $e = new AccessControlEntry;
        $e->setUser($this->getReference('adminuser'));
        $e->setGrantedBy($this->getReference('testuser1'));
        $e->setEntityId('-1');
        $e->setCreatedAt(new \DateTime('2001-01-01'));
        $e->setType('security');
        $manager->persist($e);

        //Make user2 owner of all shows
        $shows = $manager->getRepository('ActsCamdramBundle:Show')->findAll();
        foreach ($shows as $show) {
            $e = new AccessControlEntry;
            $e->setUser($this->getReference('testuser2'));
            $e->setGrantedBy($this->getReference('adminuser'));
            $e->setEntityId($show->getId());
            $e->setCreatedAt(new \DateTime('2001-01-01'));
            $e->setType('show');
            $manager->persist($e);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}