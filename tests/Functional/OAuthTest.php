<?php
namespace Camdram\Tests\Functional;

use Acts\CamdramApiBundle\Entity\ExternalApp;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Encoder\LegacyMd5Encoder;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class OAuthTest extends WebTestCase
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ExternalApp
     */
    private $app;

    /**
     * @var \Acts\CamdramAdminBundle\Tests\Client
     */
    private $client;

    /**
     * @var \Acts\CamdramAdminBundle\Tests\Client
     */
    private $userClient;

    /**
     * @var User
     */
    private $appUser;

    /**
     * @var User
     */
    private $loginUser;

    private static $db = null;

    public function setUp(): void
    {
        $this->userClient = static::createClient();

        $this->client = static::createClient();
        $this->createApiApp();
    }

    private function getEntityManager()
    {
        return $this->client->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function createApiApp()
    {
        $em= $this->getEntityManager();

        $this->appUser = new User();
        $this->appUser->setEmail('user1@camdram.net')
            ->setName('Test User');

        $encoder = new LegacyMd5Encoder();
        $hashed_password = $encoder->encodePassword('password', $this->appUser->getSalt());
        $this->appUser->setPassword($hashed_password);

        $em->persist($this->appUser);

        $app = new ExternalApp();
        $app->setUser($this->appUser)
            ->setName('Test App')
            ->setAppType('website')
            ->setDescription('Lorem ipsum');
        $app->setRedirectUrisString('');

        $em->persist($app);
        $em->flush();

        $this->app = $app;
    }

    private function login()
    {
        $this->loginUser = new User();
        $this->loginUser->setEmail('user2@camdram.net')
            ->setName('Test User 2');

        $encoder = new LegacyMd5Encoder();
        $hashed_password = $encoder->encodePassword('password', $this->loginUser->getSalt());
        $this->loginUser->setPassword($hashed_password);

        $em = $this->getEntityManager();
        $em->persist($this->loginUser);
        $em->flush();

        $crawler = $this->userClient->request('GET', '/auth/login');
        $form = $crawler->selectButton('Log in')->form();
        $form->setValues(array(
            'email' => $this->loginUser->getEmail(),
            'password' => 'password'
        ));
        $this->userClient->submit($form);
    }

    public function performOAuthUserLogin($scope)
    {
        $params = array(
            'client_id' => $this->app->getPublicId(),
            'response_type' => 'code',
            'redirect_uri' => '/authenticate',
            'scope' => $scope,
        );
        $crawler = $this->userClient->request('GET', '/oauth/v2/auth', $params);
        $form = $crawler->selectButton('Allow')->form();
        $this->userClient->followRedirects(false);
        $this->userClient->submit($form);

        $parts = parse_url($this->userClient->getResponse()->headers->get('location'));
        parse_str($parts['query'], $query);
        $code = $query['code'];

        $params = array(
            'client_id' => $this->app->getPublicId(),
            'client_secret' => $this->app->getSecret(),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => '/authenticate',
        );
        $this->client->request('GET', '/oauth/v2/token', $params);
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        return $data['access_token'];
    }

    public function testLoginFlow()
    {
        $this->login('user1@camdram.net', 'password');
        $token = $this->performOAuthUserLogin('user_shows');
        $this->assertTrue(is_string($token));

        $this->client->request('GET', '/auth/account.json?access_token='.$token);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($data['name'], "Test User 2");
    }

// Temporarily removed by Charlie Jonas <charlie@charliejonas.co.uk> to bodge a fix for #622
//
//    public function testRememberAuthorization()
//    {
//        $this->login('user1@camdram.net', 'password');
//        $token = $this->performOAuthUserLogin('');
//        $this->assertTrue(is_string($token));
//
//        //Go to auth page a second time
//        $params = array(
//            'client_id' => $this->app->getPublicId(),
//            'response_type' => 'code',
//            'redirect_uri' => '/authenticate',
//            'scope' => '',
//        );
//        $this->userClient->request('GET', '/oauth/v2/auth', $params);
//        $this->assertEquals(302, $this->userClient->getResponse()->getStatusCode());
//    }


    public function testCreateShowNoScope()
    {
        $this->login('user1@camdram.net', 'password');
        $token = $this->performOAuthUserLogin('');

        $data = array(
            'show' => array(
                'name' => 'Test Show', 'category' => 'drama'
            )
        );

        $this->client->request('POST', '/shows.json?access_token='.$token, $data);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(403, $data['error']['code']);
    }

    public function testCreateShowWriteScope()
    {
        $this->login('user1@camdram.net', 'password');
        $token = $this->performOAuthUserLogin('api_write');

        $data = array(
            'show' => array(
                'name' => 'Test Show', 'category' => 'drama'
            )
        );

        $this->client->request('POST', '/shows.json?access_token='.$token, $data);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    private function createSocietyWithOwner()
    {
        $em = $this->getEntityManager();

        $society = new Society();
        $society->setName('Test Society');
        $society->setShortName('Test Society');
        $em->persist($society);
        $em->flush();

        $aclProvider = $this->client->getContainer()->get('camdram.security.acl.provider');
        $aclProvider->grantAccess($society, $this->loginUser, $this->appUser);
    }

    public function testEditSocietyWithoutScope()
    {
        $this->login('user1@camdram.net', 'password');
        $this->createSocietyWithOwner();
        $token = $this->performOAuthUserLogin('api_write');

        $this->client->request('PUT', '/societies/test-society.json?access_token='.$token);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(403, $data['error']['code']);
    }

    public function testEditSocietyWithScope()
    {
        $this->login('user1@camdram.net', 'password');
        $this->createSocietyWithOwner();
        $token = $this->performOAuthUserLogin('api_write_org');

        $params = array(
            'society' => array('name' => 'New name')
        );
        $this->client->request('PATCH', '/societies/test-society.json?access_token='.$token, $params);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }
}
