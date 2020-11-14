<?php

namespace Acts\CamdramSecurityBundle\DataFixtures;

use Acts\CamdramBundle\DataFixtures\ShowFixtures;
use Acts\CamdramBundle\DataFixtures\SocietyFixtures;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AccessControlEntryFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Make the admin users admins
        // Note that as the admins are now actual people they get SUPER_ADMIN
        // status out of the box rather than mere adminship.
        for ($i = 0; $this->hasReference("adminuser[$i]"); $i++) {
            $e = new AccessControlEntry();
            $e->setUser($this->getReference("adminuser[$i]"));
            $e->setGrantedBy($this->getReference('testuser1'));
            $e->setEntityId(-1);
            $e->setCreatedAt(new \DateTime());
            $e->setType('security');
            $manager->persist($e);
        }

        //Make user2 owner of all shows
        $shows = $manager->getRepository(\Acts\CamdramBundle\Entity\Show::class)->findAll();
        foreach ($shows as $show) {
            $e = new AccessControlEntry();
            $e->setUser($this->getReference('testuser2'));
            $e->setGrantedBy($this->getReference('testuser1'));
            $e->setEntityId($show->getId());
            $e->setCreatedAt(new \DateTime('2001-01-01'));
            $e->setType('show');
            $manager->persist($e);
        }

        // society1admin owns society 1
        $e = new AccessControlEntry();
        $soc1 = $manager->getRepository(\Acts\CamdramBundle\Entity\Society::class)->findOneBy(['name' => 'Society 1']);
        $e->setUser($this->getReference('society1adminuser'));
        $e->setGrantedBy($this->getReference('testuser1'));
        $e->setEntityId($soc1->getId());
        $e->setCreatedAt(new \DateTime('2001-01-01'));
        $e->setType('society');
        $manager->persist($e);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ShowFixtures::class,
            SocietyFixtures::class
        ];
    }
}
