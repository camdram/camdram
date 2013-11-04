<?php

namespace Acts\CamdramBackendBundle\Features\Context;

use Acts\CamdramBackendBundle\DataFixtures\ORM\AccessControlEntryFixtures;
use Acts\CamdramBackendBundle\DataFixtures\ORM\UserFixtures;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Exception\ExpectationException;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\Step;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use PHPUnit_Framework_ExpectationFailedException as AssertException;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

if(function_exists('xdebug_disable')) { xdebug_disable(); }

/**
 * Feature context.
 */
class CamdramContext extends MinkContext
    implements KernelAwareInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;
    protected $parameters;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
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

    protected function truncateTable($entity_name)
    {
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
        $metadata = $em->getMetadataFactory()->getMetadataFor($entity_name);
        $platform = $em->getConnection()->getDatabasePlatform();
        $em->getConnection()->executeUpdate("DELETE FROM " . $metadata->getQuotedTableName($platform));
    }

    protected function purgeDatabase(array $fixtures, $append = false)
    {
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $self = $this;
        $executor->execute($fixtures, $append);
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
     * @return
     * @throws \RuntimeException
     */
    protected function getSymfonyProfile()
    {
        $client = $this->getSession()->getDriver()->getClient();

        $profile = $client->getProfile();

        if (false === $profile) {
            throw new \RuntimeException(
                'Emails cannot be tested as the profiler is '.
                'disabled.'
            );
        }

        return $profile;
    }

    /**
     * @Given /^I should receive an email at "(?P<email>[^"]+)" with:$/
     */
    public function iShouldReceiveAnEmail($email,  PyStringNode $text)
    {
        $error     = sprintf('No message sent to "%s"', $email);
        $profile   = $this->getSymfonyProfile();
        $collector = $profile->getCollector('swiftmailer');

        foreach ($collector->getMessages() as $message) {
            // Checking the recipient email and the X-Swift-To
            // header to handle the RedirectingPlugin.
            // If the recipient is not the expected one, check
            // the next mail.
            $correctRecipient = array_key_exists(
                $email, $message->getTo()
            );
            $headers = $message->getHeaders();
            $correctXToHeader = false;
            if ($headers->has('X-Swift-To')) {
                $correctXToHeader = array_key_exists($email,
                    $headers->get('X-Swift-To')->getFieldBodyModel()
                );
            }

            if (!$correctRecipient && !$correctXToHeader) {
                continue;
            }

            try {
                // checking the content
                return assertContains(
                    $text->getRaw(), $message->getBody()
                );
            } catch (AssertException $e) {
                $error = sprintf(
                    'An email has been found for "%s" but without '.
                    'the text "%s".', $email, $text->getRaw()
                );
            }
        }

        throw new ExpectationException($error, $this->getSession());
    }

    /**
     * @BeforeScenario @reset
     */
    public function reset(ScenarioEvent $event)
    {
        $this->getSession()->reset();
    }

    /**
     * @BeforeScenario @cleanUsers
     */
    public function cleanUsers(ScenarioEvent $event)
    {
        $fixtures = array(new UserFixtures(), new AccessControlEntryFixtures());
        $this->truncateTable('ActsCamdramSecurityBundle:ExternalUser');
        $this->truncateTable('ActsCamdramBundle:User');
        $this->purgeDatabase($fixtures, true);
    }

    /**
     * @BeforeScenario @cleanDatabase
     */
    public function cleanDatabase(ScenarioEvent $event)
    {
        $paths = array();
        foreach ($this->kernel->getBundles() as $bundle) {
            $paths[] = $bundle->getPath().'/DataFixtures/ORM';
        }

        $loader = new DataFixturesLoader($this->kernel->getContainer());
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
        }
        $fixtures = $loader->getFixtures();
        $this->purgeDatabase($fixtures);
    }

    /**
     * @Given /^I am logged in as "([^"]*)"$/
     * @When /^I log in as "([^"]*)"$/
     */
    public function login($email)
    {
        $this->visit('/login');
        $this->fillField('form_email', $email);
        $this->fillField('form_password', 'password');
        $this->pressButton('login_button');
    }

    /**
     * @Then /^I log out$/
     */
    public function iLogOut()
    {
        $this->visit('/logout');
    }

}
