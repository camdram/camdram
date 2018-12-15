<?php

namespace Acts\CamdramSecurityBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Acts\CamdramSecurityBundle\Entity\User;

class UserFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $u = new User();
        $u->setEmail('admin@camdram.net');
        $u->setName('Admin User');
        $manager->persist($u);
        $this->addReference('adminuser', $u);

        $u = new User();
        $u->setEmail('user1@camdram.net');
        $u->setName('Test User 1');
        $manager->persist($u);
        $this->addReference('testuser1', $u);

        $u = new User();
        $u->setEmail('user2@camdram.net');
        $u->setName('Test User 2');
        $manager->persist($u);
        $this->addReference('testuser2', $u);

        $u = new User();
        $u->setEmail('society1admin@camdram.net');
        $u->setName('Society 1 Admin');
        $manager->persist($u);
        $this->addReference('society1adminuser', $u);

        $manager->flush();
    }
}
