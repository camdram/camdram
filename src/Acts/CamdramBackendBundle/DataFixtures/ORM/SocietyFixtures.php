<?php
namespace Acts\CamdramBackendBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Application;

class SocietyFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i <= 10; $i++) {
            $society = new Society();
            $society->setName("Society $i");
            $society->setShortName("S-$i");
            $society->setDescription("Description of society $i");
            $manager->persist($society);
            
            if(mt_rand(0,1)==0){
                $this->addApplication($manager, $society);
            }
            
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
 
    private function addApplication(ObjectManager $manager, Society $society)
    {
        $application = new Application();
        $application->setText("Random text " . mt_rand(1,100));
        $application->setDeadlineDate(new \DateTime(mt_rand(-5,15) . " days"));
        $application->setFurtherInfo("Further Info text " . mt_rand(1,100));
        $application->setDeadlineTime(new \DateTime(mt_rand(0,23) . ":" . mt_rand(0,3) * 15));
        $application->setSociety($society);       
        $manager->persist($application);   
    }
}
