<?php

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;

/**
 * Feature context.
 */
class UserContext extends RawMinkContext
{
    private $entityManager;

    private $encoderFactory;

    public function __construct(EntityManagerInterface $entityManager, EncoderFactoryInterface $encoderFactory)
    {
        $this->entityManager = $entityManager;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @Given /^the user "([^"]*)" with the email "([^"]*)" and the password "([^"]*)"$/
     */
    public function createUser($name, $email, $password)
    {
        $user = new User();
        $user->setName($name)->setEmail($email);

        $encoder = $this->encoderFactory->getEncoder($user);
        $hashed_password = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($hashed_password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @Given /^the administrator "([^"]*)" with the email "([^"]*)" and the password "([^"]*)"$/
     */
    public function createAdminUser($name, $email, $password)
    {
        $user = $this->createUser($name, $email, $password);

        $ace = new AccessControlEntry();
        $ace->setUser($user);
        $ace->setEntityId(AccessControlEntry::LEVEL_FULL_ADMIN);
        $ace->setType('security');
        $ace->setGrantedBy($user);
        $ace->setCreatedAt(new \DateTime());
        $this->entityManager->persist($ace);
        $this->entityManager->flush();
    }

    /**
     * @When /^I log in as "([^"]*)" with "([^"]*)"$/
     * @Given /^I am logged in as "([^"]*)" with "([^"]*)"$/
     */
    public function login($email, $password)
    {
        $this->getSession()->visit('/auth/login');
        $this->getSession()->getPage()->fillField('form_email', $email);
        $this->getSession()->getPage()->fillField('form_password', $password);
        $this->getSession()->getPage()->pressButton('login_button');
    }

    /**
     * @Given /^the external "([^"]*)" user:$/
     */
    public function createExternalUser($service, TableNode $table)
    {
        $external_user = new ExternalUser();
        $external_user->setService(strtolower($service));

        foreach ($table->getRowsHash() as $field => $value) {
            switch ($field) {
                case 'name': $external_user->setName($value); break;
                case 'id': $external_user->setRemoteId($value); break;
                case 'email': $external_user->setEmail($value); break;
                case 'username': $external_user->setUsername($value); break;
                case 'picture': $external_user->setProfilePictureUrl($value); break;
                case 'user':
                    $user = $this->entityManager->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($value);
                    $external_user->setUser($user);
                    break;
            }
        }
        $this->entityManager->persist($external_user);
        $this->entityManager->flush();
    }
    
    /**
     * @Then /^I log out$/
     */
    public function iLogOut()
    {
        $this->getSession()->visit('/logout');
    }

    /**
     * @Given /^I delete the session cookie$/
     */
    public function deleteTheSessionCookie()
    {
        $cookies = $this->getSession()->getDriver()->getClient()->getCookieJar();
        $cookies->expire('MOCKSESSID');
    }
}
