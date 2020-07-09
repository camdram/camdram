<?php

namespace Acts\CamdramBundle\DataFixtures;

use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Audition;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class ShowFixtures extends Fixture implements DependentFixtureInterface
{
    private $roles = array();

    private $musicians = array();

    private $people_ids = array();

    private $design_roles = [
        "Set Designer",
        "Production Designer",
        "Artistic Director",
        "Supreme Artistic Overlord",
        "Publicity Designer"
    ];

    /** @var \Acts\CamdramBundle\Entity\PersonRepository $repo */
    private $person_repo;

    /** @var \Acts\CamdramBundle\Entity\SocietyRepository $repo */
    private $society_repo;

    /** @var \Acts\CamdramBundle\Entity\VenueRepository $repo */
    private $venue_repo;

    private $venue_ids = [];

    private $society_ids = [];

    public function __construct()
    {
        $file = __DIR__.'/../Resources/data/roles.yml';
        $this->roles = Yaml::parse(file_get_contents($file));

        $file = __DIR__.'/../Resources/data/musicians.yml';
        $this->musicians = Yaml::parse(file_get_contents($file));
    }

    /**
     * {@inheritDoc}
     * @param \Doctrine\ORM\EntityManagerInterface $manager
     */
    public function load(ObjectManager $manager)
    {
        $file = __DIR__.'/../Resources/data/shows.yml';
        $plays = Yaml::parse(file_get_contents($file));
        $max = count($plays) - 1;

        $start_date = new \DateTime('-18 months');
        $end_date = new \DateTime('+18 months');
        $diff = $end_date->diff($start_date, true);
        $total_weeks = $diff->days / 7;

        $this->person_repo = $manager->getRepository(Person::class);
        $this->people_ids = array_map(
            function ($val) {
                return $val['id'];
            },
             $this->person_repo->createQueryBuilder('p')->select('p.id')->getQuery()->getArrayResult()
        );

        $this->venue_repo = $manager->getRepository(Venue::class);
        $this->venue_ids = array_map(
            function ($val) {
                return $val['id'];
            },
            $this->venue_repo->createQueryBuilder('v')->select('v.id')->getQuery()->getArrayResult()
        );

        $this->society_repo = $manager->getRepository(Society::class);
        $this->society_ids = array_map(
            function ($val) {
                return $val['id'];
            },
            $this->society_repo->createQueryBuilder('s')->select('s.id')->getQuery()->getArrayResult()
        );

        for ($i = 0; $i < 250; $i++) {
            $show = new Show();
            $play = $plays[mt_rand(0, $max)];
            $show->setName($play['name']);
            $show->setDescription($play['description']);
            $show->setAuthor($play['author']);
            $show->setCategory($play['category']);
            $show->setAuthorised(true);

            $this->allocateSociety($show);

            $start = clone $start_date;
            $start->modify('+'.mt_rand(0, $total_weeks).' weeks');
            $manager->persist($show);

            //Use a random number to decide what sort of show it is...
            $type = mt_rand(0, 8);
            switch ($type) {
                case 0:case 1:case 2:case 3:case 4:
                    //A week-long run of a show
                    $offset = mt_rand(1, 3);
                    $start->modify('+'.$offset.' days');
                    $end = clone $start;
                    $end->modify('+'.mt_rand(3, 7 - $offset).' days');
                    $p = $this->generatePerformance($show, $start, $end);
                    $this->allocateVenue($p);
                    $show->addPerformance($p);
                    $manager->persist($p);
                    break;
                case 5:
                    //A one-night show
                    $start->modify('+'.mt_rand(0, 6).' days');
                    $p = $this->generatePerformance($show, $start, $start);
                    $manager->persist($p);
                    $this->allocateVenue($p);
                    $show->addPerformance($p);

                    break;
                case 6:
                    //a muli-venue tour
                    $num_perfs = mt_rand(2, 9);
                    $start->modify('+'.mt_rand(0, 5).' days');

                    for ($j = 0; $j <= $num_perfs; $j++) {
                        $start->modify('+'.mt_rand(1, 3).' days');
                        $p = $this->generatePerformance($show, clone $start, clone $start);
                        $p->setShow($show);
                        $this->allocateVenue($p);
                        $show->addPerformance($p);
                        $manager->persist($p);
                    }
                    break;
                case 7:case 8:
                    //a multi-week show
                    $offset = mt_rand(1, 3);
                    $start->modify('+'.$offset.' days');
                    $end = clone $start;
                    $end->modify('+'.mt_rand(3, 7 - $offset).' days');
                    $venue = null;
                    $other_venue = null;

                    for ($j = 0; $j <= 2; $j++) {
                        $start->modify('+1 week');
                        $end->modify('+1 week');
                        $p = $this->generatePerformance($show, clone $start, clone $end);
                        $p->setShow($show);
                        if ($j) {
                            $p->setVenue($venue);
                            $p->setOtherVenue($other_venue);
                        } else {
                            $this->allocateVenue($p);
                            $venue = $p->getVenue();
                            $other_venue = $p->getOtherVenue();
                        }
                        $show->addPerformance($p);
                        $manager->persist($p);
                    }

                    break;
            }
            $roles = $this->generateRoles($play['characters'], $play['category'] == 'musical');
            foreach ($roles as $role) {
                $show->addRole($role);
                $role->setShow($show);
                $manager->persist($role);
            }

            if (mt_rand(0, 1) == 1) {
                $this->addAuditions($manager, $show);
            }

            if (mt_rand(0, 1) == 1) {
                $this->addApplications($manager, $show);
            }

            if (mt_rand(0, 1) == 1) {
                $this->addTechieAdverts($manager, $show);
            }

            if (mt_rand(0, 1) == 1) {
                $this->addDesignerAdverts($manager, $show);
            }
        }
        $manager->flush();
    }

    private function allocateVenue($item)
    {
        if (mt_rand(0, 5) == 0) {
            $item->setOtherVenue('Random Venue '.mt_rand(1, 100));
        } else {
            return $item->setVenue($this->venue_repo->findOneById($this->venue_ids[mt_rand(0, count($this->venue_ids) - 1)]));
        }
    }

    private function allocateSociety(Show $show)
    {
        if (mt_rand(0, 3) == 0) {
            $show->setSocietiesDisplayList(['Random Society '.mt_rand(1, 100)]);
        } else {
            $socId = $this->society_ids[mt_rand(0, count($this->society_ids) - 1)];
            $show->setSocietiesDisplayList([$socId]);
            $show->getSocieties()->add($this->society_repo->findOneById($socId));
        }
    }

    private function generatePerformance(Show $show, \DateTime $start, \DateTime $end)
    {
        $perf = new Performance();
        $perf->setShow($show);
        $perf->setStartAt(new \DateTime($start->format('Y-m-d ').$this->generateTime()));
        $perf->setRepeatUntil($end);

        return $perf;
    }

    private function generateTime()
    {
        $is_evening = mt_rand(0, 3);
        if ($is_evening > 0) {
            $quart = mt_rand(0, 3);
            $time = new \DateTime('19:15');
            $time->modify('+'.($quart * 15).' minutes');

            return $time->format('H:i');
        } else {
            $hour = mt_rand(10, 22);
            $minute = mt_rand(0, 3) * 15;

            return $hour.':'.$minute;
        }
    }

    private function getRandomPerson()
    {
        return $this->person_repo->findOneById($this->people_ids[mt_rand(0, count($this->people_ids) - 1)]);
    }

    public function generateRoles($characters, $is_musical)
    {
        $roles = array();
        $order = 0;
        foreach ($characters as $character) {
            $role = new Role();
            $role->setType('cast');
            $role->setRole($character);
            $role->setPerson($this->getRandomPerson());
            $role->setOrder($order++);
            $roles[] = $role;
        }

        //Random decide how many of the technical roles in include
        $max = mt_rand(3, count($this->roles) - 1);
        $order = 0;
        for ($i = 0; $i <= $max; $i++) {
            $role = new Role();
            $role->setType('prod');
            $role->setRole($this->roles[$i]);
            $role->setPerson($this->getRandomPerson());
            $role->setOrder($order++);
            $roles[] = $role;
        }

        if ($is_musical) {
            $num = mt_rand(4, 10);
            $order = 0;
            for ($i = 0; $i < $num; $i++) {
                $role = new Role();
                $role->setType('band');
                $role->setRole($this->musicians[mt_rand(0, count($this->musicians) - 1)]);
                $role->setPerson($this->getRandomPerson());
                $role->setOrder($order++);
                $roles[] = $role;
            }
        }

        return $roles;
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            VenueFixtures::class,
            SocietyFixtures::class,
            UserFixtures::class,
        ];
    }

    private function addApplications(ObjectManager $manager, Show $show)
    {
        $application = new Advert();
        $application->setType(Advert::TYPE_APPLICATION)
            ->setTitle('Random text ' . mt_rand(1, 100))
            ->setExpiresAt(new \DateTime(mt_rand(-5, 15) . ' days'))
            ->setSummary('Further Info text ' . mt_rand(1, 100))
            ->setBody('Further Info text ' . mt_rand(1, 100))
            ->setContactDetails('Random Contact ' . mt_rand(1, 100) . ', random@example.com')
            ->setDisplay(mt_rand(0, 3) > 0)
            ->setShow($show);
        $manager->persist($application);
    }

    private function addDesignerAdverts(ObjectManager $manager, Show $show)
    {
        $num_roles_to_seek = mt_rand(1, count($this->design_roles) - 1);
        $roles_to_seek = $this->design_roles;
        shuffle($roles_to_seek);
        $roles_to_seek = array_splice($roles_to_seek, 0, $num_roles_to_seek);

        $advert = new Advert();
        $advert->setTitle('Designer roles for '. $show->getName())
            ->setType(Advert::TYPE_DESIGN)
            ->setSummary("We are looking for: \n* " . implode("\n* ", $roles_to_seek))
            ->setContactDetails('Random Contact ' . mt_rand(1, 100) . ', random@example.com')
            ->setDisplay(mt_rand(0, 4) > 0);

        $expiry = new \DateTime(mt_rand(-15, 60) . ' days');
        $advert->setExpiresAt($expiry);

        $body = '';
        if (mt_rand(0, 1) == 1) {
            $body = 'Short Tech Extra';
        } elseif (mt_rand(0, 1) == 1) {
            $body = "A very very long tech extra because some people don't know when to stop. You know the type,"
            . 'they will go on and on about the show hoping to persuade you to do it because it is exciting with'
            . 'scaffolding and balloons and probably a raised forestage.';
        }
        $advert->setBody($body);
        $advert->setShow($show);

        $manager->persist($advert);
    }

    private function addTechieAdverts(ObjectManager $manager, Show $show)
    {
        $num_roles_to_seek = mt_rand(1, count($this->roles) - 1);
        $roles_to_seek = $this->roles;
        shuffle($roles_to_seek);
        $roles_to_seek = array_splice($roles_to_seek, 0, $num_roles_to_seek);

        $advert = new Advert();
        $advert->setTitle('Technical roles for '. $show->getName())
            ->setType(Advert::TYPE_TECHNICAL)
            ->setSummary("We are looking for: \n* " . implode("\n* ", $roles_to_seek))
            ->setContactDetails('Random Contact ' . mt_rand(1, 100))
            ->setDisplay(mt_rand(0, 4) > 0);

        $expiry = new \DateTime(mt_rand(-15, 60) . ' days');
        $advert->setExpiresAt($expiry);

        $body = '';
        if (mt_rand(0, 1) == 1) {
            $body = 'Short Tech Extra';
        } elseif (mt_rand(0, 1) == 1) {
            $body = "A very very long tech extra because some people don't know when to stop. You know the type,"
            . 'they will go on and on about the show hoping to persuade you to do it because it is exciting with'
            . 'scaffolding and balloons and probably a raised forestage.';
        }
        $advert->setBody($body);
        $advert->setShow($show);

        $manager->persist($advert);
    }

    private function addAuditions(ObjectManager $manager, Show $show)
    {
        $advert = new Advert();
        $advert->setType(Advert::TYPE_ACTORS);
        $advert->setTitle('Auditions for '.$show->getName())
                ->setSummary('Audition details')
                ->setBody("# Audition extra text #\nLorem ipsum")
                ->setContactDetails('abc123@cam.ac.uk')
                ->setDisplay(mt_rand(0, 3) > 0)
                ->setShow($show);
        $manager->persist($advert);

        $numAuditions = mt_rand(1, 3);

        for ($i = 0; $i < $numAuditions; $i++) {
            $audition = new Audition();

            $days = mt_rand(-5, 10);
            $hour = mt_rand(10, 19);
            $minute = mt_rand(0, 3) * 15;

            $audition->setStartAt(new \DateTime("$days days $hour:$minute"));

            $endTime = clone $audition->getStartAt();
            $endTime->add(\DateInterval::createFromDateString(mt_rand(2, 4). ' hours'));
            $audition->setEndAt($endTime);

            if ($endTime > $advert->getExpiresAt()) {
                $advert->setExpiresAt($endTime);
            }

            $audition->setLocation('Random Location ' . mt_rand(1, 50));

            $advert->addAudition($audition);
            $manager->persist($audition);
        }
    }
}
