<?php
namespace Acts\CamdramBackendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\Yaml\Yaml;
use Acts\CamdramBundle\Entity\Person;

class PeopleFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $file = __DIR__.'/../../Resources/data/people.yml';
        $data = Yaml::parse(file_get_contents($file));
        foreach ($data as $item) {
            $person = new Person();
            $person->setName($item['name']);
            $person->setDescription($item['description']);
            $manager->persist($person);
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
