<?php
namespace Acts\CamdramAdminBundle\Tests\Service;

use Acts\CamdramBundle\Tests\RepositoryTestCase;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;

class UserMergerTest extends RepositoryTestCase
{
    private $merger;
    
    private $user1;
    private $user2;
    private $person;
    private $ace;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->merger = static::$kernel->getContainer()->get('acts_camdram_admin.user_merger');
        
        $this->user1 = new User();
        $this->user1->setName("Test User 1");
        $this->user1->setEmail("test1@camdram.net");
        $this->em->persist($this->user1);
        
        $this->user2 = new User();
        $this->user2->setName("Test User 2");
        $this->user2->setEmail("test2@camdram.net");
        $this->em->persist($this->user2);
        
        $this->person = new Person();
        $this->person->setName("Test User 1");
        $this->user1->setPerson($this->person);
        $this->em->persist($this->person);
        
        $this->ace = new AccessControlEntry();
        $this->ace->setUser($this->user1);
        $this->user1->addAce($this->ace);
        $this->ace->setEntityId(999);
        $this->ace->setType('test');
        $this->ace->setCreatedAt(new \DateTime);
        $this->em->persist($this->ace);
        
        $this->em->flush();
        $this->assertCount(1, $this->user1->getAces());
    }
    
    public function testMergeUsers_keepThis()
    {
        $user = $this->merger->mergeUsers($this->user1, $this->user2, true);
        
        $this->assertTrue($this->em->contains($this->user1));
        $this->assertFalse($this->em->contains($this->user2));
        $this->assertSame($this->user1, $user);
        
        $this->em->refresh($user);
        $this->assertEquals("Test User 1", $user->getName());
        $this->assertSame($this->person, $user->getPerson());
        $this->assertCount(1, $user->getAces());
        $this->assertEquals(999, $user->getAces()[0]->getEntityId());
    }
    
    public function testMergeUsers_keepOther()
    {
        $user = $this->merger->mergeUsers($this->user1, $this->user2, false);
        
        $this->assertTrue($this->em->contains($this->user2));
        $this->assertFalse($this->em->contains($this->user1));
        $this->assertSame($this->user2, $user);
        
        $this->em->refresh($user);
        $this->assertEquals("Test User 2", $user->getName());
        $this->assertSame($this->person, $user->getPerson());
        $this->assertCount(1, $user->getAces());
        $this->assertEquals(999, $user->getAces()[0]->getEntityId());
    }
}
