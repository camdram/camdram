<?php

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\Behat\Context\Step;
use Behat\Behat\Tester\Exception\PendingException;

use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Acts\CamdramAdminBundle\Service\DatabaseTools;
use Acts\CamdramBundle\Service\Time;
use FOS\ElasticaBundle\Index\Resetter;
use FOS\ElasticaBundle\Elastica\Index;

/**
 * Feature context.
 */
class SymfonyContext extends RawMinkContext
{
    private $listenersEnabled;


    private $elasticaResetter;

    private $elasticaIndex;

    public function __construct($listenersEnabled, Resetter $elasticaResetter, Index $elasticaIndex)
    {
        $this->listenersEnabled = $listenersEnabled;
        $this->elasticaResetter = $elasticaResetter;
        $this->elasticaIndex = $elasticaIndex;
    }

    /**
     * @Given /^(.*) without redirection$/
     */
    public function theRedirectionsAreIntercepted($step)
    {
        $this->getSession()->getDriver()->getClient()->followRedirects(false);

        return new Step\Given($step);
    }

    /**
     * @When /^I follow the redirection$/
     * @Then /^I should be redirected$/
     */
    public function iFollowTheRedirection()
    {
        $client = $this->getSession()->getDriver()->getClient();
        $client->followRedirects(true);
        $client->followRedirect();
    }

    /**
     * @BeforeSuite
     */
    public static function setupSuite(BeforeSuiteScope $scope)
    {
        StaticDriver::setKeepStaticConnections(true);
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(BeforeScenarioScope $scope)
    {
        Time::mockDateTime(new \DateTime('2000-07-03 15:30:00'));
        StaticDriver::beginTransaction();

        if ($this->listenersEnabled) {
            $this->elasticaResetter->resetAllIndexes();
        } 
        elseif ($scope->getScenario()->hasTag('search')
                || $scope->getFeature()->hasTag('search')) {
            throw new PendingException('Elasticsearch not configured');
        }
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(AfterScenarioScope $scope)
    {
        StaticDriver::rollBack();
    }

    /**
     * @Given /^I refresh the search index$/
     */
    public function refreshSearchIndex()
    {
        $this->elasticaIndex->refresh();
    }
}
