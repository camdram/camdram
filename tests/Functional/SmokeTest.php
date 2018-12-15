<?php

namespace Camdram\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    private $URLS = [
        "/",
        "/about",
        "/faq",
        "/privacy",
        "/user-guidelines",
        "/contact-us",
        "/societies",
        "/venues",
        "/people",
        "/auth/login/",
    ];

    /**
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    public function setUp(): void
    {
        $this->client = self::createClient(array('environment' => 'test'));
    }

    public function testSuccessful()
    {
        foreach ($this->URLS as $url) {
            $this->client->request('GET', $url);
            $response = $this->client->getResponse();

            $this->assertEquals(200, $response->getStatusCode(), "URL: $url");
            $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'), "URL: $url");
            $this->assertStringContainsString('<body', $response->getContent(), "URL: $url");
            $this->assertStringContainsString('</body>', $response->getContent(), "URL: $url");
        }
    }

}
