<?php

namespace Acts\CamdramBundle\DataFixtures;

use Acts\CamdramBundle\Entity\Society;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class SocietyFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $file = __DIR__.'/../Resources/config/colleges.yml';
        $colleges = Yaml::parse(file_get_contents($file));

        for ($i = 1; $i <= 10; $i++) {
            $society = new Society();
            $society->setName("Society $i");
            $society->setShortName("S-$i");
            $society->setDescription("Description of society $i");

            $socialMediaBitmask = mt_rand(0, 3);
            if ($socialMediaBitmask & 1) {
                $society->setFacebookId('606382879406665');
            }
            if ($socialMediaBitmask & 2) {
                $society->setTwitterId('1002481303');
            }

            $manager->persist($society);
        }

        foreach ($colleges as $college) {
            $society = new Society();
            $society->setName("Sample $college Society");
            $society->setShortName($college);
            $society->setCollege($college);
            $society->setDescription("Description of the fictious $college ".
                ($college == 'Anglia Ruskin' ? 'University' : 'College').
                " theatre society.\n\n###Content\n\ncontent content content.");

            $socialMediaBitmask = mt_rand(0, 3);
            if ($socialMediaBitmask & 1) {
                $society->setFacebookId('606382879406665');
            }
            if ($socialMediaBitmask & 2) {
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
