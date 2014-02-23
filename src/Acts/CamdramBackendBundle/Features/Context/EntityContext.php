<?php

namespace Acts\CamdramBackendBundle\Features\Context;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Entity\User;
use Behat\Behat\Context\BehatContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\Step;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Feature context.
 */
class EntityContext extends AbstractContext
{

    private $authorise_user;

    public function getAuthoriseUser()
    {
        if (!$this->authorise_user) {
            $user = new User();
            $user->setName('Authorise User')->setEmail('authoriser@camdram.net');
            $em = $this->getEntityManager();
            $em->persist($user);
            $em->flush();
            $this->authorise_user = $user;
        }
        return $this->authorise_user;
    }

    /**
     * @Given /^the show "([^"]*)" with society "([^"]*)" and venue "([^"]*)"$/
     */
    public function createShowWithSocietyAndVenue($show_name, $soc_name, $venue_name)
    {
        $show = $this->createShow($show_name);
        $society = $this->createSociety($soc_name);
        $venue = $this->createVenue($venue_name);

        $show->setSociety($society);
        $show->setVenue($venue);
        $this->getEntityManager()->flush();
    }

    public function createShow($show_name)
    {
        $show = new Show;
        $show->setName($show_name)
            ->setCategory('drama')
            ->setAuthorisedBy($this->getAuthoriseUser());
        $em = $this->getEntityManager();
        $em->persist($show);
        $em->flush();
        return $show;
    }

    public function createSociety($soc_name)
    {
        $society = new Society;
        $society->setName($soc_name)->setShortName($soc_name);
        $em = $this->getEntityManager();
        $em->persist($society);
        $em->flush();
        return $society;
    }

    public function createVenue($venue_name)
    {
        $venue = new Venue;
        $venue->setName($venue_name)->setShortName($venue_name);
        $em = $this->getEntityManager();
        $em->persist($venue);
        $em->flush();
        return $venue;
    }

    /**
     * @Given /^"([^"]*)" is the owner of the show "([^"]*)"$/
     */
    public function showOwner($email, $show_name)
    {
        $em = $this->getEntityManager();
        $user = $em->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($email);
        $show = $em->getRepository('ActsCamdramBundle:Show')->findOneByName($show_name);
        $this->kernel->getContainer()->get('camdram.security.acl.provider')->grantAccess($show, $user, $this->getAuthoriseUser());
    }


}
