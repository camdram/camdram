<?php

namespace Acts\CamdramBundle\DataFixtures;

use Acts\CamdramBundle\Entity\Position;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class PositionFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * Load fixtures, but in a static method so this can be called from
     * elsewhere without calling the parent constructors etc.
     */
    public static function loadStatic(ObjectManager $manager): void
    {
        $file = __DIR__.'/../Resources/data/roles.yml';
        $roles = Yaml::parse(file_get_contents($file));

        foreach ($roles as $role) {
            $position = new Position();
            $position->setName($role['name'])
                ->setWikiName($role['wiki'])
                ;
            foreach ($role['tags'] as $tag) {
                $position->addTagName($tag);
            }

            $manager->persist($position);
        }

        $manager->flush();
    }


    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        static::loadStatic($manager);
    }

    public static function getGroups(): array
    {
        return ['positions'];
    }
}
