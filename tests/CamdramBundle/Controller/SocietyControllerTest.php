<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Society;


class SocietyControllerTest extends RestTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->society = new Society();
        $this->society->setName("Test Society");
        $this->entityManager->persist($this->society);
        $this->entityManager->flush();

        $this->user = $this->createUser();
        $this->aclProvider->grantAccess($this->society, $this->user);
    }

    public function testSocietyAPI()
    {
        $data = $this->doJsonRequest('/societies/test-society.json');
        $this->assertEquals("Test Society", $data['name']);

        $data = $this->doXmlRequest('/societies/test-society.xml');
        $this->assertEquals("Test Society", $data->name);

        $data = $this->doJsonRequest('/societies/by-id/' . $this->society->getId() . '.json');
        $this->assertEquals("Test Society", $data['name']);
    }

    public function testSocietyList()
    {
        $crawler = $this->client->request('GET', "/societies");
        $this->assertEquals($crawler->filter('#content:contains("Test Society")')->count(), 1);
    }

    public function testSocietyEdit()
    {
        $sampletext = 'The Sample Society was founded in 1357 in the aftermath of a devastating lack of Shakespearian drama.';
        $crawler = $this->client->request('GET', "/societies/test-society");
        $this->assertEquals($crawler->filter('#content:contains("Test Society")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Society Administration")')->count(), 0);

        $this->login($this->user);
        $crawler = $this->client->request('GET', "/societies/test-society");
        $this->assertEquals($crawler->filter('#content:contains("Test Society")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Society Administration")')->count(), 1);

        $crawler = $this->click('Edit this society', $crawler);
        $form = $crawler->selectButton('Save')->form();
        $form['society[name]'] = 'Sample Society';
        $form['society[description]'] = $sampletext;
        $crawler = $this->client->submit($form);

        $this->assertEquals($crawler->filter('#content:contains("Sample Society")')->count(), 1);
        $this->assertEquals($crawler->filter("#content:contains(\"$sampletext\")")->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Society Administration")')->count(), 1);
    }
}
