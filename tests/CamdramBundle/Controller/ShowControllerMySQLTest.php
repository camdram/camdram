<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Camdram\Tests\MySQLTestCase;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;

class ShowControllerMySQLTest extends MySQLTestCase
{
    /**
     * Due to limitations of the DQL-SQLite driver, this does not test the
     * venue clash check.
     */
    public function testShowValidator()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $show = new Show();
        $show->setName("Validator Test")
            ->setCategory("comedy")
            ->setAuthorised(true);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/shows/validator-test');
        $this->assertHTTPStatus(200);
        $this->assertEquals(1, $crawler->filter('.error_panel:contains("no performances")')->count());
    }

    public function testShowValidatorMySQL()
    {
        // Run the SQLite compatible tests
        $this->testShowValidator();

        // Create entities
        $performance1 = new Performance();
        $performance1->setStartAt(new \DateTime("2000-01-01 19:30"));
        $performance1->setRepeatUntil(new \DateTime("2000-01-07"));
        $performance2 = new Performance();
        $performance2->setStartAt(new \DateTime("2000-01-05 19:30"));
        $performance2->setRepeatUntil(new \DateTime("2000-01-05"));

        $show1 = new Show();
        $show1->setName("Validator Test 1")
            ->setCategory("comedy")
            ->addPerformance($performance1)
            ->setAuthorised(true);
        $this->entityManager->persist($show1);
        $show2 = new Show();
        $show2->setName("Validator Test 2")
            ->setCategory("comedy")
            ->addPerformance($performance2)
            ->setAuthorised(true);
        $this->entityManager->persist($show2);
        $this->entityManager->flush();

        // No venue set => no clash
        $crawler = $this->client->request('GET', "/shows/{$show1->getSlug()}");
        $this->assertHTTPStatus(200);
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("no performances")')->count());
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("Validator Test 2")')->count());

        // Reset entity manager and add a venue to both shows => clash
        $this->entityManager->clear();

        $venue = new Venue();
        $venue->setName('ADC Theatre')->setShortName('ADC Theatre');
        $this->entityManager->persist($venue);

        $show1 = $this->entityManager->find(Show::class, $show1->getId());
        $show2 = $this->entityManager->find(Show::class, $show2->getId());
        $show1->getPerformances()->first()->setVenue($venue);
        $show2->getPerformances()->first()->setVenue($venue);
        $this->entityManager->persist($show1);
        $this->entityManager->persist($show2);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', "/shows/{$show1->getSlug()}");
        $this->assertHTTPStatus(200);
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("no performances")')->count());
        $this->assertEquals(1, $crawler->filter('.error_panel:contains("Validator Test 2")')->count());

        // Reset entity manager and change one date => no clash
        $this->entityManager->clear();
        $performance1 = $this->entityManager->find(Performance::class, $performance1->getId());
        $performance1->setStartAt(new \DateTime("2000-02-01 19:30"));
        $performance1->setRepeatUntil(new \DateTime("2000-02-07"));
        $this->entityManager->persist($performance1);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', "/shows/{$show1->getSlug()}");
        $this->assertHTTPStatus(200);
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("no performances")')->count());
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("Validator Test 2")')->count());

        // Reset entity manager and change one time => no clash
        $this->entityManager->clear();
        $performance1 = $this->entityManager->find(Performance::class, $performance1->getId());
        $performance1->setStartAt(new \DateTime("2000-01-01 21:30"));
        $performance1->setRepeatUntil(new \DateTime("2000-01-07"));
        $this->entityManager->persist($performance1);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', "/shows/{$show1->getSlug()}");
        $this->assertHTTPStatus(200);
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("no performances")')->count());
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("Validator Test 2")')->count());
    }
}
