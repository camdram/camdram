<?php
namespace Camdram\Tests\Functional;

use Acts\CamdramApiBundle\Entity\ExternalApp;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class LoginTest extends WebTestCase
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    private $entityManager;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->client->followRedirects();

        $container = $this->client->getKernel()->getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
    }

    public function testRegistration(): void
    {
        $crawler = $this->client->request('GET', '/auth/connect/test');
        $form = $crawler->selectButton('Submit')->form();
        $form->setValues([
            'identifier' => 1234,
            'name' => 'Test User',
            'email' => 'joe@camdram.net',
        ]);
        $crawler = $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h4', 'Register');
        $this->assertSelectorExists('#login-link');

        $form = $crawler->selectButton('Register')->form();
        $this->assertEquals($form->getValues()['external_registration[email]'], 'joe@camdram.net');
        $form->setValues([
            'external_registration[name]' => 'Joe Smith',
        ]);
        $this->client->submit($form);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#account-link', 'Joe Smith');
    }

    public function testLogin(): void
    {
        $user = new User();
        $user->setEmail('joe.smith@camdram.net')
            ->setName('Joe Smith');
        $this->entityManager->persist($user);
        $externelUser = new ExternalUser();
        $externelUser->setUsername(1234)
            ->setEmail('joe.smith@camdram.net')
            ->setName('Joe Smith')
            ->setService('test')
            ->setUser($user);
        $this->entityManager->persist($externelUser);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/auth/connect/test');
        $form = $crawler->selectButton('Submit')->form();
        $form->setValues([
            'identifier' => 1234,
            'name' => 'Test User',
            'email' => 'joe@camdram.net',
        ]);
        $crawler = $this->client->submit($form);
        $this->assertSelectorTextContains('#account-link', 'Joe Smith');
    }

    public function testLoginNoExternalUser(): void
    {
        $user = new User();
        $user->setEmail('joe@camdram.net')
            ->setName('Joe Smith');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/auth/connect/test');
        $form = $crawler->selectButton('Submit')->form();
        $form->setValues([
            'identifier' => 1234,
            'name' => 'Test User',
            'email' => 'joe@camdram.net',
        ]);
        $crawler = $this->client->submit($form);
        $this->assertSelectorTextContains('#account-link', 'Joe Smith');
    }
}