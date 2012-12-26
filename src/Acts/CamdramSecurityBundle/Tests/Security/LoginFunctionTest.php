<?php
namespace Acts\CamdramSecurityBundle\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginFunctionalTest extends WebTestCase
{
    public function testConnect()
    {
        $client = static::createClient();
        $client->request('GET', '/connect/facebook');
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\RedirectResponse', $client->getResponse());
        $target = $client->getResponse()->getTargetUrl();
        $this->assertEquals('https://www.facebook.com/dialog/oauth?response_type=code&client_id=1234&scope=email&redirect_uri=http%3A%2F%2Flocalhost%2Flogin%2Ffacebook',
                $target);
    }

}