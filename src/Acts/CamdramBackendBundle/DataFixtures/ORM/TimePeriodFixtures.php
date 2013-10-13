<?php
namespace Acts\CamdramBackendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Acts\CamdramBundle\Entity\TimePeriod;
use Acts\CamdramBundle\Entity\TimePeriodGroup;

class TimePeriodFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $start_date = new \DateTime("2000-01-01 00:00:00");
        $start_date->modify("+".(7-$start_date->format('N'))); //Rewind back to Sunday
        $end_date = new \DateTime("2001-12-31 00:00:00");

        $date = clone $start_date;
        $groups = array();
        while ($date < $end_date) {
            $g = new TimePeriodGroup();
            $g->setStartAt(clone $date);

            $month = $date->format('F');
            $date->modify("+4 weeks");
            if ($month == $date->format('F')) $date->modify("+1 week");

            $g->setEndAt(clone $date);
            $g->setName($g->getStartAt()->format("F"));
            $g->setLongName($g->getStartAt()->format("F Y"));
            $manager->persist($g);
            $groups[] = $g;
        }
        $manager->flush();

        $date = clone $start_date;
        $week_counter = 1;
        $cur_group = 0;
        while ($date < $end_date) {
            $p = new TimePeriod();
            $p->setStartAt(clone $date);
            $date->modify("+1 week");
            $p->setEndAt(clone $date);

            if ($p->getStartAt() >= $groups[$cur_group]->getEndAt()) {
                $cur_group+= 1;
                $week_counter = 1;
            }

            $p->setShortName("Week $week_counter");
            $p->setName($p->getStartAt()->format("F")." Week $week_counter");
            $p->setLongName($p->getStartAt()->format("F Y")." Week $week_counter");


            $p->setGroup($groups[$cur_group]);

            $manager->persist($p);
            $week_counter++;
        }
        $manager->flush();

        $this->setReference('start_group', $groups[0]);
        $this->setReference('end_group', $groups[count($groups)-7]);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

}