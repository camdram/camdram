<?php

namespace Acts\CamdramBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\Yaml\Yaml;
use Acts\CamdramBundle\Entity\Venue;

class VenueFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $file = __DIR__.'/../../Resources/data/venues.yml';
        $data = Yaml::parse(file_get_contents($file));
        foreach ($data as $item) {
            $venue = new Venue();
            $venue->setName($item['name']);
            $venue->setShortName('');
            $venue->setDescription($item['description']);
            $venue->setLatitude($item['latitude']);
            $venue->setLongitude($item['longitude']);
            $manager->persist($venue);
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
