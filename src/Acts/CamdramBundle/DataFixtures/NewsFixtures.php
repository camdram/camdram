<?php

namespace Acts\CamdramBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Acts\CamdramBundle\Entity\Organisation;
use Acts\CamdramBundle\Entity\News;

class NewsFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        mt_srand(microtime(true));

        foreach ($manager->getRepository('ActsCamdramBundle:Society')->findAll() as $society)
        {
            $this->generateForOrganisation($manager, $society);
        }
        foreach ($manager->getRepository('ActsCamdramBundle:Venue')->findAll() as $venue)
        {
            $this->generateForOrganisation($manager, $venue);
        }
        $manager->flush();
    }

    protected function generateForOrganisation(ObjectManager $manager, Organisation $org)
    {
        if ($org->getFacebookId())
        {
            $this->generateNews($manager, $org, 'facebook');
        }

        if ($org->getTwitterId())
        {
            $this->generateNews($manager, $org, 'twitter');
        }
    }

    protected function generateNews(ObjectManager $manager, Organisation $org, $source)
    {
        $count = mt_rand(1, 10);
        $postedAt = new \DateTime();

        for ($i = 0; $i < $count; $i++)
        {
            $postedAt->modify('-' . mt_rand(6, 144). ' hours');

            $news = new News;
            $news->setEntity($org)
                ->setSource($source)
                ->setBody($org->getName().' news entry #'.($i + 1))
                ->setPostedAt(clone $postedAt)
                ;
            
            $manager->persist($news);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            VenueFixtures::class,
            SocietyFixtures::class
        ];
    }
}