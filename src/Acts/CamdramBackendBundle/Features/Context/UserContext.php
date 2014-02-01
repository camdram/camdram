<?php

namespace Acts\CamdramBackendBundle\Features\Context;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Behat\Behat\Context\BehatContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\Step;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

//if(function_exists('xdebug_disable')) { xdebug_disable(); }

/**
 * Feature context.
 */
class UserContext extends BehatContext implements KernelAwareInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    private $tables = array();

    /**
     * @return \Behat\MinkExtension\Context\MinkContext
     */
    private function getMinkContext()
    {
        return $this->getMainContext()->getSubcontext('mink');
    }

    private function getDbTools()
    {
        return $this->kernel->getContainer()->get('acts_camdram_backend.database_tools');
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
     * @Given /^the user "([^"]*)" with the email "([^"]*)" and the password "([^"]*)"$/
     */
    public function createUser($name, $email, $password)
    {
        $user = new User();
        $user->setName($name)->setEmail($email);

        $factory = $this->kernel->getContainer()->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $hashed_password = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($hashed_password);

        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $em->persist($user);
        $em->flush();
        return $user;
    }

    /**
     * @Given /^the administrator "([^"]*)" with the email "([^"]*)" and the password "([^"]*)"$/
     */
    public function createAdminUser($name, $email, $password)
    {
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $user = $this->createUser($name, $email, $password);

        $ace = new AccessControlEntry();
        $ace->setUser($user);
        $ace->setEntityId(AccessControlEntry::LEVEL_FULL_ADMIN);
        $ace->setType('security');
        $ace->setGrantedBy($user);
        $ace->setCreatedAt(new \DateTime());
        $em->persist($ace);
        $em->flush();
    }

    /**
      * @When /^I log in as "([^"]*)" with "([^"]*)"$/
      * @Given /^I am logged in as "([^"]*)" with "([^"]*)"$/
     */
    public function login($email, $password)
    {
        $this->getMinkContext()->visit('/auth/login');
        $this->getMinkContext()->fillField('form_email', $email);
        $this->getMinkContext()->fillField('form_password', $password);
        $this->getMinkContext()->pressButton('login_button');
    }


    private $external_login_data = array(
        'facebook' => array('name' => 'Test Facebook User', 'username' => 'test.facebook.user',
            'id' => 1234, 'email' => 'test@facebook.com', 'picture' => 'http://test.com/facebook-profile-picture.png'),
        'google' => array('name' => 'Test Google User', 'username' => 'test.google.user',
            'id' => 5678, 'email' => 'test@gmail.com', 'picture' => 'http://test.com/google-profile-picture.png'),
        'raven' => array('name' => null, 'username' => 'abc123', 'id' => null, 'email' => 'abc123@cam.ac.uk', 'picture' => null),
    );

    /**
     * @Given /^I am logged in using "([^"]*)" as "([^"]*)"$/
     * @When /^I log in using "([^"]*)" as "([^"]*)"$/
     */
    public function iAmLoggedInUsing($service, $name)
    {
        $service = strtolower($service);
        $this->getMinkContext()->visit('/extauth/redirect/'.$service);
        $data = $this->external_login_data[$service];
        $this->getMinkContext()->fillField('ID', $data['id']);
        $this->getMinkContext()->fillField('Username', $data['username']);
        $this->getMinkContext()->fillField('Name', $name);
        $this->getMinkContext()->fillField('Email', $data['email']);
        $this->getMinkContext()->fillField('picture', $data['picture']);
        $this->getMinkContext()->pressButton('Submit');
    }

    /**
     * @Then /^I log out$/
     */
    public function iLogOut()
    {
        $this->getMinkContext()->visit('/logout');
    }

}
