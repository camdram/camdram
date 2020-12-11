<?php

namespace Camdram\Tests\CamdramBundle\Entity;

use Camdram\Tests\RepositoryTestCase;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Position;

class RoleTest extends RepositoryTestCase
{
    private $user;

    private $show;

    private $person;

    public function setUp(): void
    {
        parent::setUp();

        $this->show = new Show();
        $this->show->setName('Test Show');
        $this->show->setCategory('drama');
        $this->show->setAuthorised(true);
        $this->em->persist($this->show);

        $this->person = new Person;
        $this->person->setName('Test Person');
        $this->em->persist($this->person);

        $this->em->flush();
    }

    public function testPosition()
    {
        $position = new Position;
        $position->setName('Technical Director')
            ->addTagName('Technical Director');
        $this->em->persist($position);
        $this->em->flush();

        $role = new Role();
        $role->setRole('Technical Director')
            ->setShow($this->show)
            ->setPerson($this->person)
            ->setType('prod')
            ->setOrder(0);
        $this->show->addRole($role);

        $this->em->persist($role);
        $this->em->flush();
        
        $this->assertEquals($position, $role->getPosition());
    }
}
