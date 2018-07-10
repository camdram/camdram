<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Camdram\Tests\RepositoryTestCase;
use Acts\CamdramBundle\Entity\Person;

class PersonRepositoryTest extends RepositoryTestCase
{
    /**
     * @return \Acts\CamdramBundle\Entity\PersonRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('ActsCamdramBundle:Person');
    }

    public function testFindCanonicalPerson_none()
    {
        $person = $this->getRepository()->findCanonicalPerson('Fred Smith');
        $this->assertEquals(null, $person);
    }

    public function testFindCanonicalPerson_one()
    {
        $person = new Person();
        $person->setName('Fred Smith');
        $this->em->persist($person);
        $this->em->flush();

        $person = $this->getRepository()->findCanonicalPerson('Fred Smith');
        $this->assertEquals('Fred Smith', $person->getName());
    }

    public function testFindCanonicalPerson_mapped()
    {
        $person1 = new Person();
        $person1->setName('Fred Smith');
        $this->em->persist($person1);

        $person2 = new Person();
        $person2->setName('Freddie Smith');
        $person2->setMappedTo($person1);
        $this->em->persist($person2);

        $this->em->flush();

        $person = $this->getRepository()->findCanonicalPerson('Freddie Smith');
        $this->assertEquals('Fred Smith', $person->getName());
    }
}
