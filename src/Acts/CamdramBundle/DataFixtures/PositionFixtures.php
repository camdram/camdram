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
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
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

    public static function getGroups(): array
    {
        return ['positions'];
    }
}
