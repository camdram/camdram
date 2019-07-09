<?php

namespace Camdram\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Acts\CamdramBundle\Service\Time;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Audition;
use Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\CamdramBundle\Entity\Application;
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

    public function setUp()
    {
        $this->client = self::createClient(array('environment' => 'test'));

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
        $this->assertContains('text/html', $response->headers->get('Content-Type'), "URL: $url");
        $this->assertContains('<body', $response->getContent(), "URL: $url");
        $this->assertContains('</body>', $response->getContent(), "URL: $url");
        return $response;
    }

    public function fetchText($url)
    {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), "URL: $url");
        $this->assertContains('text/plain', $response->headers->get('Content-Type'), "URL: $url");
        $this->assertNotContains('<html>', $response->getContent(), "URL: $url");
        return $response;
    }

    public function fetchRss($url)
    {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), "URL: $url");
        $this->assertContains('application/rss+xml', $response->headers->get('Content-Type'), "URL: $url");
        $this->assertContains('</rss>', $response->getContent(), 'URL: $url');
        return $response;
    }

    public function testAuditions()
    {
        $show = $this->createShow('Test Show', '2000-02-01');
        $show->setAudextra('blah');
        $audition = new Audition;
        $audition->setDisplay(0)
            ->setStartAt(new \Datetime('2000-01-15 10:00'))
            ->setEndAt(new \DateTime('2000-01-15 18:00'))
            ->setLocation('Somewhere')
            ->setNonScheduled(false)
            ->setShow($show);
        $this->entityManager->persist($audition);
        $this->entityManager->flush();

        $response = $this->fetchText('/vacancies/auditions.txt');
        $this->assertContains('Test Show', $response->getContent());

        $response = $this->fetchHtml('/vacancies/auditions');
        $this->assertContains('Test Show', $response->getContent());

        $response = $this->fetchRss('/vacancies/auditions.rss');
        $this->assertContains('Test Show', $response->getContent());
    }

    public function testTechieAdverts()
    {
        $show = $this->createShow('Test Show', '2000-02-01');

        $techieAdvert = new TechieAdvert;
        $techieAdvert->setDisplay(0)
            ->setPositions("Technical Director\nLighting Designer")
            ->setContact('Contact foo@bar.com')
            ->setDeadline(true)
            ->setExpiry(new \DateTime('2000-01-15'))
            ->setShow($show);
        $this->entityManager->persist($techieAdvert);
        $this->entityManager->flush();

        $response = $this->fetchText('/vacancies/techies.txt');
        $this->assertContains('Test Show', $response->getContent());

        $response = $this->fetchHtml('/vacancies/techies');
        $this->assertContains('Test Show', $response->getContent());

        $response = $this->fetchRss('/vacancies/techies.rss');
        $this->assertContains('Test Show', $response->getContent());
    }

    public function testApplications()
    {
        $show = $this->createShow('Test Show', '2000-02-01');

        $application = new Application;
        $application
            ->setText('Lorem ipsum')
            ->setDeadlineDate(new \DateTime('2000-01-15'))
            ->setDeadlineTime(new \DateTime('15:00'))
            ->setFurtherInfo('Contact foo@bar.com')
            ->setShow($show);
        $this->entityManager->persist($application);
        $this->entityManager->flush();

        $response = $this->fetchText('/vacancies/applications.txt');
        $this->assertContains('Test Show', $response->getContent());

        $response = $this->fetchHtml('/vacancies/applications');
        $this->assertContains('Test Show', $response->getContent());

        $response = $this->fetchRss('/vacancies/applications.rss');
        $this->assertContains('Test Show', $response->getContent());
    }

}
