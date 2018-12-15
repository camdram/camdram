<?php

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

use Camdram\Tests\RestTestCase;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;

class DefaultControllerTest extends RestTestCase
{

    public function testToolbar()
    {
        $user = $this->createUser('John Smith', 'user1@camdram.net');
        $this->login($user);

        $crawler = $this->client->request('GET', '/');
        $this->assertEquals($crawler->filter('#account-link:contains("John Smith")')->count(), 1);

        //Log out
        $crawler = $this->client->request('GET', '/logout');
        $this->assertEquals($crawler->filter('#login-link:contains("Log in")')->count(), 1);
    }

    public function testToolbarAdmin()
    {
        $user = $this->createUser('John Smith', 'admin@camdram.net');
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/');
        $this->assertEquals($crawler->filter('#account-link:contains("John Smith")')->count(), 1);
        $this->assertEquals($crawler->filter('#admin-link:contains("Administration")')->count(), 1);
    }

}