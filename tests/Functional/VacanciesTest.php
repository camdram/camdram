<?php

namespace Camdram\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Acts\CamdramBundle\Service\Time;
use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Audition;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramSecurityBundle\Entity\User;

class VacanciesTest extends WebTestCase
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    private $entityManager;

    /**
     * @var User
     */
    private $user;

    public function setUp(): void
    {
        $this->client = self::createClient(array('environment' => 'test'));
        $this->client->followRedirects();

        $container = $this->client->getKernel()->getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');

        $this->user = new User;
        $this->user->setName("Test User")->setEmail("admin@camdram.net");
        $this->entityManager->persist($this->user);

        Time::mockDateTime(new \DateTime('2000-01-01'));
    }

    private function createShow($name, $startDate)
    {
        $show = new Show;
        $show->setName($name)
            ->setAuthorised(true)
            ->setCategory('drama');

        $performance = new Performance;
        $performance->setStartAt(new \DateTime($startDate. ' 19:30'));
        $performance->setRepeatUntil(new \DateTime($startDate));
        $show->addPerformance($performance);

        $this->entityManager->persist($show);
        $this->entityManager->persist($performance);
        return $show;
    }

    public function fetchHtml($url)
    {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), "URL: $url");
        $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'), "URL: $url");
        $this->assertStringContainsString('<body', $response->getContent(), "URL: $url");
        $this->assertStringContainsString('</body>', $response->getContent(), "URL: $url");
        return $response;
    }

    public function fetchText($url)
    {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), "URL: $url");
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'), "URL: $url");
        $this->assertStringNotContainsString('<html>', $response->getContent(), "URL: $url");
        return $response;
    }

    public function fetchRss($url)
    {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), "URL: $url");
        $this->assertStringContainsString('application/rss+xml', $response->headers->get('Content-Type'), "URL: $url");
        $this->assertStringContainsString('</rss>', $response->getContent(), 'URL: $url');
        return $response;
    }

    public function testAuditions()
    {
        $show = $this->createShow('Test Show', '2000-02-01');
        $advert = new Advert;
        $advert->setType(Advert::TYPE_ACTORS)
            ->setName('blah')
            ->setSummary('Lorem ipsum')
            ->setBody('Lorem ipsum')
            ->setContactDetails('foo@bar.com')
            ->setShow($show);
        $this->entityManager->persist($advert);
        $audition = new Audition;
        $audition->setStartAt(new \Datetime('2000-01-15 10:00'))
            ->setEndAt(new \DateTime('2000-01-15 18:00'))
            ->setLocation('Somewhere')
            ->setAdvert($advert);
        $this->entityManager->persist($audition);
        $this->entityManager->flush();

        $response = $this->fetchText('/vacancies/auditions.txt');
        $this->assertStringContainsString('Test Show', $response->getContent());

        $response = $this->fetchHtml('/vacancies/auditions');
        $this->assertStringContainsString('Test Show', $response->getContent());

        $response = $this->fetchRss('/vacancies/auditions.rss');
        $this->assertStringContainsString('Test Show', $response->getContent());
    }

    public function testTechieAdverts()
    {
        $show = $this->createShow('Test Show', '2000-02-01');

        $techieAdvert = new Advert;
        $techieAdvert->setType(Advert::TYPE_TECHNICAL)
            ->setName('Technical roles for test show')
            ->setSummary("Technical Director\nLighting Designer")
            ->setBody('Lorem ipsum')
            ->setContactDetails('foo@bar.com')
            ->setExpiresAt(new \DateTime('2000-01-15'))
            ->setShow($show);
        $this->entityManager->persist($techieAdvert);
        $this->entityManager->flush();

        $response = $this->fetchText('/vacancies/techies.txt');
        $this->assertStringContainsString('Test Show', $response->getContent());

        $response = $this->fetchHtml('/vacancies/techies');
        $this->assertStringContainsString('Test Show', $response->getContent());

        $response = $this->fetchRss('/vacancies/techies.rss');
        $this->assertStringContainsString('Test Show', $response->getContent());
    }

    public function testApplications()
    {
        $show = $this->createShow('Test Show', '2000-02-01');

        $application = new Advert;
        $application
            ->setType(Advert::TYPE_APPLICATION)
            ->setName('Applications for test show')
            ->setSummary('Lorem ipsum')
            ->setBody('Lorem ipsum')
            ->setExpiresAt(new \DateTime('2000-01-15 15:00'))
            ->setContactDetails('Contact foo@bar.com')
            ->setShow($show);
        $this->entityManager->persist($application);
        $this->entityManager->flush();

        $response = $this->fetchText('/vacancies/applications.txt');
        $this->assertStringContainsString('Test Show', $response->getContent());

        $response = $this->fetchHtml('/vacancies/applications');
        $this->assertStringContainsString('Test Show', $response->getContent());

        $response = $this->fetchRss('/vacancies/applications.rss');
        $this->assertStringContainsString('Test Show', $response->getContent());
    }

}
