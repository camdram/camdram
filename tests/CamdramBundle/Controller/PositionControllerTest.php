<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Camdram\Tests\RestTestCase;

use Acts\CamdramBundle\Entity\Position;

class PositionControllerTest extends RestTestCase
{
    /**
     * @var Position
     */
    private $position;

    public function setUp(): void
    {
        parent::setUp();

        $this->position = new Position();
        $this->position->setName("Test Position");
        $this->entityManager->persist($this->position);
        $this->entityManager->flush();
    }

    public function testPositionList()
    {
        $crawler = $this->client->request('GET', '/positions');
        $this->assertCrawlerHasN('#content h2:contains("Roles")', 1, $crawler);
        $this->assertCrawlerHasN('#content a:contains("' . $this->position->getName() . '")', 1, $crawler);
    }
}
