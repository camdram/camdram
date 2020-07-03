<?php

namespace Acts\CamdramBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Acts\CamdramBundle\Entity\Society;

class SocietyFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $society = new Society();
            $society->setName("Society $i");
            $society->setShortName("S-$i");
            $society->setDescription("Description of society $i");

            $socialMediaBitmask = mt_rand(0, 3);
            if ($socialMediaBitmask & 0x01)
            {
                $society->setFacebookId('606382879406665');
            }
            if ($socialMediaBitmask & 0x02)
            {
                $society->setTwitterId('1002481303');
            }

            $manager->persist($society);
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
