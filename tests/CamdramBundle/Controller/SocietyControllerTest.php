<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Society;


class SocietyControllerTest extends RestTestCase
{

    public function testSociety()
    {
        $society = new Society;
        $society->setName("Test Society");
        $this->entityManager->persist($society);
        $this->entityManager->flush();

        $data = $this->doJsonRequest('/societies/test-society.json');
        $this->assertEquals("Test Society", $data['name']);

        $data = $this->doXmlRequest('/societies/test-society.xml');
        $this->assertEquals("Test Society", $data->name);

        $data = $this->doJsonRequest('/societies/by-id/' . $society->getId() . '.json');
        $this->assertEquals("Test Society", $data['name']);
    }

}
