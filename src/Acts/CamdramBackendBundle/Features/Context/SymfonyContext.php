<?php

namespace Acts\CamdramBackendBundle\Features\Context;

use Acts\CamdramBackendBundle\DataFixtures\ORM\AccessControlEntryFixtures;
use Acts\CamdramBackendBundle\DataFixtures\ORM\UserFixtures;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Exception\ExpectationException;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\Step;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use PHPUnit_Framework_ExpectationFailedException as AssertException;

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
    public function resetSphinx()
    {
        if ($this->kernel->getContainer()->has('acts.sphinx_realtime.resetter')) {
            $resetter = $this->kernel->getContainer()->get('acts.sphinx_realtime.resetter');
            $indexManager = $this->kernel->getContainer()->get('acts.sphinx_realtime.index_manager');
            foreach ($indexManager->getIndexes() as $index) {
                $resetter->resetIndex($index->getId());
            }
        }
    }
}
