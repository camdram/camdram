<?php

namespace Acts\CamdramApiBundle\Features\Context;

use Acts\CamdramApiBundle\Entity\ExternalApp;
use Acts\CamdramBackendBundle\Features\Context\AbstractContext;
use Acts\CamdramBackendBundle\Features\Context\EntityContext;
use Acts\CamdramBackendBundle\Features\Context\SymfonyContext;
use Acts\CamdramBackendBundle\Features\Context\UserContext;
use Acts\CamdramSecurityBundle\Entity\User;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Driver\BrowserKitDriver;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Feature context.
 */
class RestContext extends AbstractContext
{

    /**
     * @var Response
     */
    private $response;

    /** @var  ExternalApp */
    private $app;

    /**
     * @var string
     */
    private $access_token = '';

    /**
     * @return \Acts\CamdramBackendBundle\Test\Client
     */
    private function getClient()
    {
        return $this->getMinkContext()->getSession()->getDriver()->getClient();
    }

    public function createUser($name, $email)
    {
        $user = new User();
        $user->setName($name)
            ->setEmail($email)
            ->setPassword('kdsfsdf');

        $em = $this->getEntityManager();

        $em->persist($user);
        $em->flush();
        return $user;
    }

    /**
     * @BeforeScenario
     */
    public function createApiApp(ScenarioEvent $event)
    {
        $user = $this->getMainContext()->getSubcontext('entity')->getAuthoriseUser();
        $manager = $this->kernel->getContainer()->get('fos_oauth_server.client_manager.default');

        $app = new ExternalApp();
        $app->setUser($user)
            ->setName('Test App')
            ->setAppType('website')
            ->setDescription('Lorem ipsum');

        $manager->updateClient($app);
        $this->app = $app;
    }

    /**
     * @Given /^I have an OAuth access token$/
     */
    public function iHaveAnOauthAccessToken()
    {
        $params = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->app->getPublicId(),
            'client_secret' => $this->app->getSecret(),
        );
        $this->getClient()->request('GET', '/oauth/v2/token', $params);
        $data = json_decode($this->getClient()->getResponse()->getContent());
        $this->access_token = $data->access_token;
    }

    /**
     * @When /^I send a ([A-Z]+) request to "([^"]*)"$/
     */
    public function iSendARequestTo($method, $uri)
    {
        $uri .= '?access_token='.$this->access_token;
        $this->getClient()->request($method, $uri);
        $this->response = $this->getClient()->getResponse();
    }

    /**
     * @Then /^the response code should be (\d+)$/
     */
    public function theResponseCodeShouldBe($code)
    {
        assertEquals($code, $this->response->getStatusCode());
    }

    /**
     * @Given /^the response type should be "([^"]*)"$/
     */
    public function theResponseTypeShouldBe($type)
    {
        assertContains($type, $this->response->headers->get('Content-Type'));
    }

    /**
     * @When /^I send a ([A-Z]+) request to "([^"]*)" with the "([^"]*)" data$/
     */
    public function iSendARequestToWithTheData($method, $uri, $key, TableNode $table)
    {
        $data = array();
        foreach ($table->getRows() as $row) {
            $data[$row[0]] = $row[1];
        }
        $formData = json_encode(array($key => $data));
        $uri .= '?access_token=' . $this->access_token;
        $server = array('CONTENT_TYPE' => "application/json");

        $this->getClient()->request($method, $uri,array(),array(),$server,$formData);
        $this->response = $this->getClient()->getResponse();
    }

    /**
     * @Given /^when I go to the location$/
     */
    public function whenIGoToTheLocation()
    {
        $this->iSendARequestTo('GET', $this->response->headers->get('Location'));
    }

    /**
     * @Then /^the response key "([^"]*)" should equal "([^"]*)"$/
     */
    public function theResponseKeyShouldEqual($key, $value)
    {
        $data = json_decode($this->response->getContent(), true);
        assertEquals($data[$key], $value);
    }

}
