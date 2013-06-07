<?php
namespace Acts\CamdramBackendBundle\DataFixtures\ORM;

use Acts\CamdramBundle\Entity\Role;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\Yaml\Yaml;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;

class ShowFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    private $roles = array();

    private $musicians = array();

    private $people_ids = array();

    /** @var \Acts\CamdramBundle\Entity\PersonRepository $repo */
    private $person_repo;

    public function __construct()
    {
        $file = __DIR__.'/../../Resources/data/roles.yml';
        $this->roles = Yaml::parse(file_get_contents($file));

        $file = __DIR__.'/../../Resources/data/musicians.yml';
        $this->musicians = Yaml::parse(file_get_contents($file));
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $file = __DIR__.'/../../Resources/data/shows.yml';
        $plays = Yaml::parse(file_get_contents($file));
        $max = count($plays)-1;
        mt_srand(1423.343);

        $start_date = $this->getReference('start_group')->getStartAt();
        $end_date = $this->getReference('end_group')->getEndAt();
        $diff = $end_date->diff($start_date, true);
        $total_weeks = $diff->days/7;

        $this->person_repo = $manager->getRepository('ActsCamdramBundle:Person');
        $this->people_ids = array_map(function($val) { return $val['id']; },
             $this->person_repo->createQueryBuilder('p')->select('p.id')->getQuery()->getArrayResult());

        $this->venue_repo = $manager->getRepository('ActsCamdramBundle:Venue');
        $this->venue_ids = array_map(function($val) { return $val['id']; },
            $this->venue_repo->createQueryBuilder('v')->select('v.id')->getQuery()->getArrayResult());

        $this->society_repo = $manager->getRepository('ActsCamdramBundle:Society');
        $this->society_ids = array_map(function($val) { return $val['id']; },
            $this->society_repo->createQueryBuilder('s')->select('s.id')->getQuery()->getArrayResult());

        for ($i=0; $i<150; $i++) {
            $show = new Show;
            $play = $plays[mt_rand(0,$max)];
            $show->setName($play['name']);
            $show->setDescription($play['description']);
            $show->setAuthor($play['author']);
            $show->setCategory($play['category']);
            $this->allocateSociety($show);

            $start = clone $start_date;
            $start->modify("+".mt_rand(0, $total_weeks).' weeks');
            $manager->persist($show);

            //Use a random number to decide what sort of show it is...
            $type = mt_rand(0,8);
            switch ($type) {
                case 0:case 1:case 2:case 3:case 4:
                    //A week-long run of a show
                    $offset = mt_rand(1,3);
                    $start->modify("+".$offset." days");
                    $end = clone $start;
                    $end->modify("+".mt_rand(3,7-$offset)." days");
                    $p = $this->generatePerformance($show, $start, $end);
                    $this->allocateVenue($show);
                    $show->addPerformance($p);
                    $manager->persist($p);
                    break;
                case 5:
                    //A one-night show
                    $start->modify("+".mt_rand(0,6)." days");
                    $p = $this->generatePerformance($show, $start, $start);
                    $manager->persist($p);
                    $this->allocateVenue($show);
                    $show->addPerformance($p);

                    break;
                case 6:
                    //a muli-venue tour
                    $num_perfs = mt_rand(2,9);
                    $start->modify("+".mt_rand(0,5)." days");

                    for ($j=0; $j<=$num_perfs; $j++) {
                        $start->modify("+".mt_rand(1,3)." days");
                        $p = $this->generatePerformance($show, clone $start, clone $start);
                        $p->setShow($show);
                        $this->allocateVenue($p);
                        $show->addPerformance($p);
                        $manager->persist($p);
                    }
                    break;
                case 7:case 8:
                    //a multi-week show
                    $offset = mt_rand(1,3);
                    $start->modify("+".$offset." days");
                    $end = clone $start;
                    $end->modify("+".mt_rand(3,7-$offset)." days");
                    $this->allocateVenue($show);

                    for ($j=0; $j<=2; $j++) {
                        $start->modify("+1 week");
                        $end->modify("+1 week");
                        $p = $this->generatePerformance($show, clone $start, clone $end);
                        $p->setShow($show);
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
            $show->updateVenues();
            $show->updateTimes();
        }
        $manager->flush();
    }

    private function allocateVenue($item)
    {
        if (mt_rand(0,5) == 0) {
            $item->setVenueName('Random Venue '.mt_rand(1,100));
        }
        else {
            return $item->setVenue($this->venue_repo->findOneById($this->venue_ids[mt_rand(0,count($this->venue_ids)-1)]));
        }
    }

    private function allocateSociety(Show $show)
    {
        if (mt_rand(0,3) == 0) {
            $show->setSocietyName('Random Society '.mt_rand(1,100));
        }
        else {
            $show->setSociety($this->society_repo->findOneById($this->society_ids[mt_rand(0,count($this->society_ids)-1)]));
        }
    }

    private function generatePerformance(Show $show, \DateTime $start, \DateTime $end)
    {
        $perf = new Performance;
        $perf->setShow($show);
        $perf->setStartDate($start);
        $perf->setEndDate($end);
        $perf->setTime($this->generateTime());
        return $perf;
    }

    private function generateTime()
    {
        $is_evening = mt_rand(0,3);
        if ($is_evening > 0) {
            $quart = mt_rand(0,3);
            $time = new \DateTime("19:15");
            $time->modify("+".($quart*15)." minutes");
            return $time;
        }
        else {
            $hour = mt_rand(10, 22);
            $minute = mt_rand(0,3)*15;
            return new \DateTime($hour.':'.$minute);
        }
    }

    private function getRandomPerson()
    {
        return $this->person_repo->findOneById($this->people_ids[mt_rand(0,count($this->people_ids)-1)]);
    }

    public function generateRoles($characters, $is_musical) {
        $roles = array();
        $order=0;
        foreach ($characters as $character) {
            $role = new Role();
            $role->setType('cast');
            $role->setRole($character);
            $role->setPerson($this->getRandomPerson());
            $role->setOrder($order++);
            $roles[] = $role;
        }

        //Random decide how many of the technical roles in include
        $max = mt_rand(3,count($this->roles)-1);
        $order=0;
        for ($i=0; $i <= $max; $i++) {
            $role = new Role();
            $role->setType('prod');
            $role->setRole($this->roles[$i]);
            $role->setPerson($this->getRandomPerson());
            $role->setOrder($order++);
            $roles[] = $role;
        }

        if ($is_musical) {
            $num = mt_rand(4, 10);
            $order=0;
            for ($i=0; $i < $num; $i++) {
                $role = new Role();
                $role->setType('band');
                $role->setRole($this->musicians[mt_rand(0,count($this->musicians)-1)]);
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
    public function getOrder()
    {
        return 2;
    }
}