<?php
namespace Camdram\Tests;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Sabre\VObject;

use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Entity\User;

class RestTestCase extends WebTestCase
{
    use ArraySubsetAsserts;

    /**
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    protected $entityManager;

    /**
     * @var AclProvider
     */
    protected $aclProvider;

    public function setUp(): void
    {
        $this->client = self::createClient(array('environment' => 'test'));
        $this->client->followRedirects(true);

        $container = $this->client->getKernel()->getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->aclProvider = $container->get('camdram.security.acl.provider');
    }

    protected function login(User $user)
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

    protected function logout(): void
    {
        $session = $this->client->getContainer()->get('session');
        $session->invalidate();
        $session->save();
        $this->client->getCookieJar()->clear();
    }

    protected function createUser()
    {
        $user = new User();
        $user->setName("Test User")->setEmail("test@camdram.net");
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    protected function doJsonRequest($url, $params = [])
    {
        $this->client->request('GET', $url, $params);

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/json', $response->headers->get('Content-Type'));

        return json_decode($response->getContent(), true);
    }

    protected function doXmlRequest($url, $params = [])
    {
        $this->client->request('GET', $url, $params);

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/xml', $response->headers->get('Content-Type'));
        return new \SimpleXMLElement($response->getContent());
    }

    protected function doICalRequest($url)
    {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/calendar', $response->headers->get('Content-Type'));
        $vobj = VObject\Reader::read($response->getContent());
        $this->assertEquals(0, count($vobj->validate()));
        return $vobj;
    }
}
