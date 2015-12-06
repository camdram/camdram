<?php

namespace Acts\CamdramBackendBundle\Features\Context;

use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\TimePeriod;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Entity\User;

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
        $show = new Show();
        $show->setName($show_name)
            ->setCategory('drama')
            ->setAuthorisedBy($this->getAuthoriseUser());
        $em = $this->getEntityManager();
        $em->persist($show);
        $em->flush();

        return $show;
    }

    /**
     * @Given /^the society "([^"]*)"$/
     */
    public function createSociety($soc_name)
    {
        $society = new Society();
        $society->setName($soc_name)->setShortName($soc_name);
        $em = $this->getEntityManager();
        $em->persist($society);
        $em->flush();

        return $society;
    }

    /**
     * @Given /^the venue "([^"]*)"$/
     */
    public function createVenue($venue_name)
    {
        $venue = new Venue();
        $venue->setName($venue_name)->setShortName($venue_name);
        $em = $this->getEntityManager();
        $em->persist($venue);
        $em->flush();

        return $venue;
    }

    /**
     * @Given /^the person "([^"]*)"$/
     */
    public function createPerson($person_name)
    {
        $person = new Person();
        $person->setName($person_name);
        $em = $this->getEntityManager();
        $em->persist($person);
        $em->flush();
        return $person;
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

    /**
     * @Given /^"([^"]*)" is the owner of the (?:society|venue) "([^"]*)"$/
     */
    public function organisationOwner($email, $org_name)
    {
        $em = $this->getEntityManager();
        $user = $em->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($email);
        $org = $em->getRepository('ActsCamdramBundle:Organisation')->findOneByName($org_name);
        $this->kernel->getContainer()->get('camdram.security.acl.provider')->grantAccess($org, $user, $this->getAuthoriseUser());
    }

    /**
     * @Given /^"([^"]*)" is linked to the person "([^"]*)"$/
     */
    public function isLinkedToThePerson($email, $person_name)
    {
        $em = $this->getEntityManager();
        $user = $em->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($email);
        $person = $em->getRepository('ActsCamdramBundle:Person')->findOneByName($person_name);
        $user->setPerson($person);
        $em->flush();
    }


    /**
     * @Given /^the show "([^"]*)" starting in (\-?[0-9]+) days? and lasting ([0-9]+) days? at ([0-9]+:[0-9]+)$/
     */
    public function createShowWithDates($show_name, $days, $length, $time)
    {
        $show = $this->createShow($show_name);

        $start_date = $this->getCurrentTime();
        $day_of_week = $start_date->format('N');
        if ($day_of_week < 7) {
            $start_date->modify('-'.$day_of_week.' days');
        }
        $start_date->modify('+'.$days.' day');
        $end_date = clone $start_date;
        $end_date->modify('+'.$length.' days');

        $performance = new Performance();
        $performance->setStartDate($start_date);
        $performance->setEndDate($end_date);
        $performance->setTime(new \DateTime($time));
        $performance->setShow($show);
        $show->addPerformance($performance);

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^the time period "([^"]*)" from "([^"]*)" to "([^"]*)"$/
     */
    public function createTimePeriod($name, $from, $to)
    {
        $period = new TimePeriod();
        $period->setName($name);
        $period->setFullName($name);
        $period->setShortName($name);
        $period->setStartAt(new \DateTime($from));
        $period->setEndAt(new \DateTime($to));
        $em = $this->getEntityManager();
        $em->persist($period);
        $em->flush();
    }
}
