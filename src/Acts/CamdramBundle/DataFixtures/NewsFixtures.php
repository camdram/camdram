<?php

namespace Acts\CamdramBundle\DataFixtures;

use Doctrine\Persistence\ObjectManager;
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
        foreach ($manager->getRepository('\Acts\CamdramBundle\Entity\Society')->findAll() as $society)
        {
            $this->generateForOrganisation($manager, $society);
        }
        foreach ($manager->getRepository('\Acts\CamdramBundle\Entity\Venue')->findAll() as $venue)
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
            $postedAt->modify('-' . mt_rand(6*3600, 144*3600). ' seconds');

            $news = new News;
            // Tries to give a fairly comprehensive mix of lengths, links and images.
            $body = ['', 'Hi <a href="https://twitter.com/camdram">@Camdram</a>! '][mt_rand(0, 1)].
                $org->getName() .
                [' is excited to announce ', ' has some great news! We\'re performing ',
                ' has a really very long post. Rehersals are well underway for our very exciting new show.'.
                ' We\'re sure you\'ll want to hear all about it, and maybe come see it! So anyway our latest show is ',
                ' wants you to buy tickets for '][mt_rand(0, 3)] .
                [ 'The Merchant of ', 'Three Men in ', 'The End of ', 'Surviving ', 'Much Ado About ',
                         'Waiting for ', 'Who\'s Afraid of ', 'Angels in ', 'Pirates of ', 'A Streetcar Named '][mt_rand(0, 9)] .
                [ 'Oxford.', 'the Van of Life.', ' Philosophy.', ' Amsterdam.',
                        'St John\'s.', 'Panto.', 'Cindies.', 'Addenbrooke\'s.', 'Week 5.'][mt_rand(0, 8)];
            $picture = ['https://placekitten.com/'.mt_rand(195, 210).'/200', null][mt_rand(0, 1)];
            $news->setEntity($org)
                ->setSource($source)
                ->setBody($body)
                ->setPicture($picture)
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
