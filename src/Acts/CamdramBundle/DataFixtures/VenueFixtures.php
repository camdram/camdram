<?php

namespace Acts\CamdramBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Yaml\Yaml;
use Acts\CamdramBundle\Entity\Venue;

class VenueFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $file = __DIR__.'/../Resources/data/venues.yml';
        $data = Yaml::parse(file_get_contents($file));
        mt_srand(microtime(true));

        foreach ($data as $item) {
            $venue = new Venue();
            $venue->setName($item['name']);
            $venue->setShortName('');
            $venue->setDescription($item['description']);
            $venue->setLatitude($item['latitude']);
            $venue->setLongitude($item['longitude']);

            $socialMediaBitmask = mt_rand(0, 3);
            if ($socialMediaBitmask & 0x01)
            {
                $venue->setFacebookId('606382879406665');
            }
            if ($socialMediaBitmask & 0x02)
            {
                $venue->setTwitterId('1002481303');
            }

            $manager->persist($venue);
        }
        $manager->flush();
    }
}
