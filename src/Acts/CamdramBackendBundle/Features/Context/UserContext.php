<?php

namespace Acts\CamdramBackendBundle\Features\Context;

use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Behat\Gherkin\Node\TableNode;

/**
 * Feature context.
 */
class UserContext extends AbstractContext
{
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

        $em = $this->getEntityManager();

        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @Given /^the administrator "([^"]*)" with the email "([^"]*)" and the password "([^"]*)"$/
     */
    public function createAdminUser($name, $email, $password)
    {
        $em = $this->getEntityManager();

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

    /**
     * @Given /^the external "([^"]*)" user:$/
     */
    public function createExternalUser($service, TableNode $table)
    {
        $external_user = new ExternalUser();
        $external_user->setService(strtolower($service));

        $em = $this->getEntityManager();

        foreach ($table->getRowsHash() as $field => $value) {
            switch ($field) {
                case 'name' : $external_user->setName($value); break;
                case 'id'   : $external_user->setRemoteId($value); break;
                case 'email' : $external_user->setEmail($value); break;
                case 'username' : $external_user->setUsername($value); break;
                case 'picture' : $external_user->setProfilePictureUrl($value); break;
                case 'user':
                    $user = $em->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($value);
                    $external_user->setUser($user);
                    break;
            }
        }
        $em->persist($external_user);
        $em->flush();
    }

    /**
     * @Given /^I am logged in using "([^"]*)" as "([^"]*)"$/
     * @When /^I log in using "([^"]*)" as "([^"]*)"$/
     */
    public function iAmLoggedInUsing($service, $username)
    {
        $service = strtolower($service);
        $this->getMinkContext()->visit('/extauth/redirect/'.$service);
        $this->getMinkContext()->fillField('Username', $username);
        $this->getMinkContext()->pressButton('Submit');
    }

    /**
     * @Given /^I am logged in using "([^"]*)" as "([^"]*)" with name "([^"]*)"$/
     * @When /^I log in using "([^"]*)" as "([^"]*)" with name "([^"]*)"$/
     */
    public function iLogInUsingAsWithName($service, $username, $name)
    {
        $service = strtolower($service);
        $this->getMinkContext()->visit('/extauth/redirect/'.$service);
        $this->getMinkContext()->fillField('Username', $username);
        $this->getMinkContext()->fillField('Name', $name);
        $this->getMinkContext()->pressButton('Submit');
    }

    /**
     * @Given /^I am logged in using "([^"]*)" as "([^"]*)" with email "([^"]*)"$/
     * @When /^I log in using "([^"]*)" as "([^"]*)" with email "([^"]*)"$/
     */
    public function iLogInUsingAsWithEmail($service, $username, $email)
    {
        $service = strtolower($service);
        $this->getMinkContext()->visit('/extauth/redirect/'.$service);
        $this->getMinkContext()->fillField('Username', $username);
        $this->getMinkContext()->fillField('Email', $email);
        $this->getMinkContext()->pressButton('Submit');
    }

    /**
     * @Then /^I log out$/
     */
    public function iLogOut()
    {
        $this->getMinkContext()->visit('/logout');
    }

    /**
     * @Given /^I delete the session cookie$/
     */
    public function deleteTheSessionCookie()
    {
        $cookies = $this->getMinkContext()->getSession()->getDriver()->getClient()->getCookieJar();
        $cookies->expire('MOCKSESSID');
    }
}
