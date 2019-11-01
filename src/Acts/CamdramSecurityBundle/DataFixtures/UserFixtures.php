<?php

namespace Acts\CamdramSecurityBundle\DataFixtures;

use Acts\CamdramSecurityBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Yaml\Yaml;

class UserFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $parameters = Yaml::parse(file_get_contents('app/config/parameters.yml'))["parameters"];
        $adminData = json_decode($parameters['default_admin_idents']);

        foreach ($adminData as $key => $admin) {
            $u = new User();
            $u->setEmail($admin->email);
            $u->setName($admin->name);
            $manager->persist($u);
            // Currently only possible to pass Entities around so can't just
            // send an array of everything to the ACE fixture maker.
            $this->addReference("adminuser[$key]", $u);
        }

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
