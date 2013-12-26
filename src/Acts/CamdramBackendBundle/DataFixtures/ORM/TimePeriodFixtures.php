<?php
namespace Acts\CamdramBackendBundle\DataFixtures\ORM;

use Acts\CamdramBundle\Entity\WeekName;
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
        $periods = array();
        while ($date < $end_date) {
            $p = new TimePeriod();
            $p->setStartAt(clone $date);

            $month = $date->format('F');
            $date->modify("+4 weeks");
            if ($month == $date->format('F')) $date->modify("+1 week");

            $p->setEndAt(clone $date);
            $p->setShortName($p->getStartAt()->format("M"));
            $p->setName($p->getStartAt()->format("F"));
            $p->setFullName($p->getStartAt()->format("F Y"));
            $manager->persist($p);
            $periods[] = $p;
        }
        $manager->flush();

        $date = clone $start_date;
        $week_counter = 1;
        $cur_period = 0;
        while ($date < $end_date) {
            $w = new WeekName();
            $w->setStartAt(clone $date);
            $date->modify("+1 week");

            if ($w->getStartAt() >= $periods[$cur_period]->getEndAt()) {
                $cur_period+= 1;
                $week_counter = 1;
            }

            $w->setShortName("Week $week_counter");
            $w->setName($w->getStartAt()->format("F")." Week $week_counter");

            $manager->persist($w);
            $week_counter++;
        }
        $manager->flush();

        $this->setReference('start_group', $periods[0]);
        $this->setReference('end_group', $periods[count($periods)-7]);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

}