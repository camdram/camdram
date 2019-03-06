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
            $this->assertContains('That show does not exist', $this->client->getResponse()->getContent());
        }

        // Test no performances
        foreach (['', '.csv'] as $format) {
            $crawler = $this->client->request('GET', '/shows/'.$show->getSlug().'/signinsheet' . $format);
            $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
            $this->assertContains('There are no performances associated with this show', $this->client->getResponse()->getContent());
        }

        // Test with a performance
        $perf = new Performance();
        $perf->setShow($show);
        $perf->setStartAt(new \DateTime('next Tuesday 19:45'));
        $perf->setRepeatUntil(new \DateTime('next Tuesday 19:45 +4 days'));
        $show->addPerformance($perf);
        $this->entityManager->persist($perf);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        // HTML mode
        $crawler = $this->client->request('GET', '/shows/'.$show->getSlug().'/signinsheet');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $table = $crawler->filter('table')->first();
        $this->assertRegExp('/Tech.*Dress.*Tue.*19:45.*Wed.*19:45.*Thu.*19:45.*Fri.*19:45.*Sat.*19:45/',
            $table->filter('tr')->first()->html());
    }

}
?>
