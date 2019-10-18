<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;

class DefaultControllerTest extends WebTestCase
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
     * @var AclProvider
     */
    private $aclProvider;

    public function setUp(): void
    {
        $this->client = self::createClient(['environment' => 'test']);
        $this->client->followRedirects(true);

        $container = $this->client->getKernel()->getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->aclProvider = $container->get('camdram.security.acl.provider');
    }

    private function createUser($name, $email, $password)
    {
        $user = new User();
        $user->setName($name)->setEmail($email);

        $user->setPassword(md5($password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function testLogin()
    {
        $user = $this->createUser('John Smith', 'user1@camdram.net', 'password1');

        //Log in using a password
        $crawler = $this->client->request('GET', '/auth/login');
        $buttonCrawlerNode = $crawler->selectButton('Log in');
        $form = $buttonCrawlerNode->form(array(
            'email'    => 'user1@camdram.net',
            'password' => 'password1',
        ));
        $crawler = $this->client->submit($form);

        $this->assertEquals($crawler->filter('#account-link:contains("John Smith")')->count(), 1);

        //Log out
        $crawler = $this->client->request('GET', '/logout');
        $this->assertEquals($crawler->filter('#login-link:contains("Log in")')->count(), 1);
    }

    public function testLoginAdmin()
    {
        $user = $this->createUser('John Smith', 'admin@camdram.net', 'password2');
        $this->aclProvider->grantAdmin($user);

        $crawler = $this->client->request('GET', '/auth/login');
        $buttonCrawlerNode = $crawler->selectButton('Log in');
        $form = $buttonCrawlerNode->form(array(
            'email'    => 'admin@camdram.net',
            'password' => 'password2',
        ));
        $crawler = $this->client->submit($form);

        $this->assertEquals($crawler->filter('#account-link:contains("John Smith")')->count(), 1);
        $this->assertEquals($crawler->filter('#admin-link:contains("Administration")')->count(), 1);
    }

    public function testRememberSession()
    {
        $user = $this->createUser('John Smith', 'user1@camdram.net', 'password1');

        //Log in using a password
        $crawler = $this->client->request('GET', '/auth/login');
        $buttonCrawlerNode = $crawler->selectButton('Log in');
        $form = $buttonCrawlerNode->form(array(
            'email'    => 'user1@camdram.net',
            'password' => 'password1',
        ));
        $this->client->submit($form);

        //Clear session and check account settings page triggers a re-authentication
        $this->client->getCookieJar()->expire('MOCKSESSID');
        $crawler = $this->client->request('GET', '/auth/account');
        $this->assertEquals($crawler->filter('#content:contains("you must log in again")')->count(), 1);

        //Log in again
        $buttonCrawlerNode = $crawler->selectButton('Log in');
        $form = $buttonCrawlerNode->form(array(
            'email'    => 'admin@camdram.net',
            'password' => 'password2',
        ));
        $crawler = $this->client->submit($form);
        $this->assertEquals($crawler->filter('#content:contains("Account Settings")')->count(), 1);

        //Clear session again and check user is still remembered
        $this->client->getCookieJar()->expire('MOCKSESSID');
        $crawler = $this->client->request('GET', '/');
        $this->assertEquals($crawler->filter('#account-link:contains("John Smith")')->count(), 1);
    }
}