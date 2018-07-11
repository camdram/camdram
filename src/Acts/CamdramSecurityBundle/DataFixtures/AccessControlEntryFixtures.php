<?php

namespace Acts\CamdramSecurityBundle\DataFixtures;

use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\DataFixtures\ShowFixtures;

class AccessControlEntryFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        //Make the admin user an admin
        $e = new AccessControlEntry();
        $e->setUser($this->getReference('adminuser'));
        $e->setGrantedBy($this->getReference('testuser1'));
        $e->setEntityId('-2');
        $e->setCreatedAt(new \DateTime('2001-01-01'));
        $e->setType('security');
        $manager->persist($e);

        //Make user2 owner of all shows
        $shows = $manager->getRepository('ActsCamdramBundle:Show')->findAll();
        foreach ($shows as $show) {
            $e = new AccessControlEntry();
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
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ShowFixtures::class
        ];
    }
}
