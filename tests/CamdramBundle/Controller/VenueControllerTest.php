<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Venue;


class VenueControllerTest extends RestTestCase
{
    public function testVenue()
    {
        $venue = new Venue;
        $venue->setName("Test Venue");
        $this->entityManager->persist($venue);
        $this->entityManager->flush();

        $data = $this->doJsonRequest('/venues/test-venue.json');
        $this->assertEquals("Test Venue", $data['name']);

        $data = $this->doXmlRequest('/venues/test-venue.xml');
        $this->assertEquals("Test Venue", $data->name);

        $data = $this->doJsonRequest('/venues/by-id/' . $venue->getId() . '.json');
        $this->assertEquals("Test Venue", $data['name']);
    }

}
