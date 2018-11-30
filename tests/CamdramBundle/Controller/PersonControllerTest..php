<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Person;


class PersonControllerTest extends RestTestCase
{

    public function testPerson()
    {
        $person = new Person;
        $person->setName("John Smith");
        $this->entityManager->persist($person);
        $this->entityManager->flush();

        $data = $this->doJsonRequest('/people/john-smith.json');
        $this->assertEquals("John Smith", $data['name']);

        $data = $this->doXmlRequest('/people/john-smith.xml');
        $this->assertEquals("John Smith", $data->name);

        $data = $this->doJsonRequest('/people/by-id/' . $person->getId() . '.json');
        $this->assertEquals("John Smith", $data['name']);
    }

}
