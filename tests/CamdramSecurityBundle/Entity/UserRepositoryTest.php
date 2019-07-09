<?php

namespace Camdram\Tests\CamdramSecurityBundle\Entity;

use Camdram\Tests\RepositoryTestCase;
use Acts\CamdramSecurityBundle\Entity\User;

class UserRepositoryTest extends RepositoryTestCase
{
    /**
     * @return \Acts\CamdramBundle\Entity\PersonRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('ActsCamdramSecurityBundle:User');
    }

    public function testEmailSearchMultipleMatches()
    {
        $user1 = new User();
        $user1->setName('Test User 1');
        $user1->setEmail('abc123');

        $user2 = new User();
        $user2->setName('Test User 2');
        $user2->setEmail('abc123@cam.ac.uk');

        $this->em->persist($user1);
        $this->em->persist($user2);
        $this->em->flush();

        $user = $this->getRepository()->findOneByEmail('abc123@cam.ac.uk');
        $this->assertTrue($user instanceof User);
    }
}
