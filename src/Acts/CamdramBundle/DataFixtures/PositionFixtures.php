<?php

namespace Acts\CamdramBundle\DataFixtures;

use Acts\CamdramBundle\Entity\Position;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class PositionFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $file = __DIR__.'/../Resources/data/roles.yml';
        $roles = Yaml::parse(file_get_contents($file));

        foreach ($roles as $role) {
            $position = new Position();
            $position->setPrimaryName($role['name'])
                ->setWikiName($role['wiki'])
                ;

            $manager->persist($position);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
