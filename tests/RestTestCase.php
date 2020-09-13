<?php
namespace Camdram\Tests;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Sabre\VObject;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
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

    protected function createUser(
        string $name = "Test User", string $email = "test@camdram.net",
        int $admin_rank = 0): User
    {
        $user = new User();
        $user->setName($name)->setEmail($email);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        if ($admin_rank) {
            $this->aclProvider->grantAdmin($user, $admin_rank);
        }
        $this->entityManager->flush();

        return $user;
    }

    protected function createShow(string $name): Show
    {
        $show = new Show();
        $show->setName($name);
        $show->setCategory("drama");
        $show->setAuthorised(true);
        $this->entityManager->persist($show);
        $performance = new Performance();
        $performance->setStartAt(new \DateTime('Tuesday 19:45 next week'));
        $performance->setRepeatUntil(new \DateTime('Saturday 19:45 next week'));
        $show->addPerformance($performance);
        $this->entityManager->persist($performance);
        $this->entityManager->flush();

        return $show;
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

    protected function click(string $linkText, $crawler)
    {
        $link = $crawler->selectLink($linkText)->link();
        return $this->client->request('GET', $link->getUri());
    }

    protected function assertHTTPStatus(int $status): void
    {
        $this->assertEquals($status, $this->client->getResponse()->getStatusCode());
    }
}
