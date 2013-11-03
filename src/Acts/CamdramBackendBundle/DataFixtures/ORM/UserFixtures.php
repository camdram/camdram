<?php
namespace Acts\CamdramBackendBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\Yaml\Yaml;
use Acts\CamdramBundle\Entity\User;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $u = new User;
        $u->setEmail('admin@camdram.net');
        $u->setPassword(md5('password'));
        $u->setName('Admin User');
        $manager->persist($u);
        $this->addReference('adminuser', $u);

        $u = new User;
        $u->setEmail('user1@camdram.net');
        $u->setPassword(md5('password'));
        $u->setName('Test User 1');
        $manager->persist($u);
        $this->addReference('testuser1', $u);

        $u = new User;
        $u->setEmail('user2@camdram.net');
        $u->setPassword(md5('password'));
        $u->setName('Test User 2');
        $manager->persist($u);
        $this->addReference('testuser2', $u);

        $u = new User;
        $u->setEmail('society1admin@camdram.net');
        $u->setPassword(md5('password'));
        $u->setName('Society 1 Admin');
        $manager->persist($u);
        $this->addReference('society1adminuser', $u);

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