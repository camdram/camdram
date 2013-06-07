<?php
namespace Acts\CamdramBackendBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Acts\CamdramSecurityBundle\Entity\Group;

class GroupFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $g = new Group;
        $g->setName('Administrators');
        $g->setMenuName('Administration');
        $g->setShortName('admin');
        $g->addUser($this->getReference('adminuser'));
        $manager->persist($g);

        $g = new Group;
        $g->setName('Society 1 Committee');
        $g->setMenuName('Society 1');
        $g->setShortName('society-1');
        $g->addUser($this->getReference('society1adminuser'));
        $manager->persist($g);

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