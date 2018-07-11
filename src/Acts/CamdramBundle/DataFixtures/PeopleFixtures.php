<?php

namespace Acts\CamdramBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Yaml\Yaml;
use Acts\CamdramBundle\Entity\Person;

class PeopleFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $file = __DIR__.'/../Resources/data/people.yml';
        $data = Yaml::parse(file_get_contents($file));
        foreach ($data as $item) {
            $person = new Person();
            $person->setName($item['name']);
            $person->setDescription($item['description']);
            $manager->persist($person);
        }
        $manager->flush();
    }
}
