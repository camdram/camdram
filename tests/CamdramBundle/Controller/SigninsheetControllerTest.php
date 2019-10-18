<?php
namespace Camdram\Tests\CamdramBundle\Controller\Show;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Show;

class SigninsheetControllerTest extends RestTestCase
{

    public function testAddRemoveRole()
    {
        $show = new Show();
        $show->setName('Signin sheet test')->setCategory('drama')->setAuthorised(true);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        // Test non-existent
        foreach (['', '.csv'] as $format) {
            $crawler = $this->client->request('GET', '/shows/non-existent-test/signinsheet'.$format);
            $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
            $this->assertStringContainsString('That show does not exist', $this->client->getResponse()->getContent());
        }

        // Test no performances
        foreach (['', '.csv'] as $format) {
            $crawler = $this->client->request('GET', '/shows/'.$show->getSlug().'/signinsheet' . $format);
            $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
            $this->assertStringContainsString('There are no performances associated with this show', $this->client->getResponse()->getContent());
        }

        // Test with a performance, spanning a timezone change for the UK.
        $perf = new Performance();
        $startAt = new \DateTime('2019-03-28 19:45');
        $endAt = (clone $startAt)->modify("+4 days");
        $perf->setShow($show);
        $perf->setStartAt($startAt);
        $perf->setRepeatUntil($endAt);
        $show->addPerformance($perf);
        $this->entityManager->persist($perf);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        // HTML mode
        $crawler = $this->client->request('GET', '/shows/'.$show->getSlug().'/signinsheet');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $table = $crawler->filter('table')->first();
        $this->assertRegExp('/Tech.*Dress.*Thu.*19:45.*Fri.*19:45.*Sat.*19:45.*Sun.*19:45.*Mon.*19:45/',
            $table->filter('tr')->first()->html());
    }

}
?>
