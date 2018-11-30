<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Component\BrowserKit\Cookie;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;


class ShowControllerTest extends WebTestCase
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

    /**
     * @var Show
     */
    private $show;

    public function setUp()
    {
        $this->client = self::createClient(array('environment' => 'test'));

        $container = $this->client->getKernel()->getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');

        $this->aclProvider = $container->get('camdram.security.acl.provider');

        $this->show = new Show();
        $this->show->setName("Test Show")
            ->setCategory('drama')
            ->setAuthorised(true);
        $this->entityManager->persist($this->show);
        $this->entityManager->flush();

    }

    private function login(User $user)
    {
        $session = $this->client->getContainer()->get('session');

        //$token = new UsernamePasswordToken('admin', null, 'public', array('ROLE_ADMIN'));
        $token = new OAuthToken('test_token', $user->getRoles());
        $token->setUser($user);
        $session->set('_security_public', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function createUser()
    {
        $user = new User();
        $user->setName("Test User")->setEmail("test@camdram.net");
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function testViewLoggedOut()
    {
        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 0);
    }

    public function testViewAsShowOwner()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAccess($this->show, $user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 1);
    }

    public function testViewAsAdmin()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 1);
    }

    public function testEditShow()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAccess($this->show, $user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/test-show/edit');
        $this->assertEquals($crawler->filter('#content:contains("Edit Show")')->count(), 1);

        $input = $crawler->filter('input[name="show[name]"]');
        $this->assertEquals("Test Show", $input->attr('value'));
    }
}