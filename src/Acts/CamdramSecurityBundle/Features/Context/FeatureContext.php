<?php

namespace Acts\CamdramSecurityBundle\Features\Context;

use Acts\CamdramBackendBundle\DataFixtures\ORM\AccessControlEntryFixtures;
use Acts\CamdramBackendBundle\DataFixtures\ORM\UserFixtures;
use Acts\CamdramBackendBundle\Features\Context\CamdramContext;
use Behat\Behat\Event\ScenarioEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Feature context.
 */
class FeatureContext extends CamdramContext //MinkContext if you want to test web
                  implements KernelAwareInterface
{
    private $external_login_data = array(
        'facebook' => array('name' => 'Test Facebook User', 'username' => 'test.facebook.user',
            'id' => 1234, 'email' => 'test@facebook.com', 'picture' => 'http://test.com/facebook-profile-picture.png'),
        'google' => array('name' => 'Test Google User', 'username' => 'test.google.user',
            'id' => 5678, 'email' => 'test@gmail.com', 'picture' => 'http://test.com/google-profile-picture.png'),
        'raven' => array('name' => null, 'username' => 'abc123', 'id' => null, 'email' => 'abc123@cam.ac.uk', 'picture' => null),
    );

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
     * @Given /^I am logged in using "([^"]*)"$/
     * @When /^I log in using "([^"]*)"$/
     */
    public function iAmLoggedInUsing($service)
    {
        $service = strtolower($service);
        $this->visit('/extauth/redirect/'.$service);
        $data = $this->external_login_data[$service];

        $this->fillField('ID', $data['id']);
        $this->fillField('Username', $data['username']);
        $this->fillField('Name', $data['name']);
        $this->fillField('Email', $data['email']);
        $this->fillField('picture', $data['picture']);

        $this->pressButton('Submit');
    }

}
