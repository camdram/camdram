<?php

namespace Acts\CamdramBackendBundle\Features\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Event\ScenarioEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\Behat\Context\Step;

/**
 * Feature context.
 */
class SymfonyContext extends BehatContext implements KernelAwareInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @return \Behat\MinkExtension\Context\MinkContext
     */
    private function getMinkContext()
    {
        return $this->getMainContext()->getSubcontext('mink');
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^(.*) without redirection$/
     */
    public function theRedirectionsAreIntercepted($step)
    {
        $this->getMinkContext()->getSession()->getDriver()->getClient()->followRedirects(false);

        return new Step\Given($step);
    }

    /**
     * @When /^I follow the redirection$/
     * @Then /^I should be redirected$/
     */
    public function iFollowTheRedirection()
    {
        $client = $this->getMinkContext()->getSession()->getDriver()->getClient();
        $client->followRedirects(true);
        $client->followRedirect();
    }

    /**
     * @BeforeScenario
     */
    public function reset(ScenarioEvent $event)
    {
        $this->getMinkContext()->getSession()->restart();
    }

    /**
     * @BeforeScenario
     */
    public function resetDb()
    {
        $this->kernel->getContainer()->get('acts_camdram_backend.database_tools')->resetDatabase();
    }

    /**
     * @BeforeScenario
     */
    public function resetElasticsearch()
    {
        $resetter = $this->kernel->getContainer()->get('fos_elastica.resetter');
        $resetter->resetAllIndexes();
    }

    /**
     * @Given /^I refresh the search index$/
     */
    public function refreshSearchIndex()
    {
        $this->kernel->getContainer()->get('fos_elastica.index.autocomplete')->refresh();
    }
}
