<?php

namespace Acts\CamdramBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{

    const HTML_URLS = [
        "/",
        "/societies",
        "/venues",
        "/vacancies/auditions",
        "/vacancies/techies",
        "/vacancies/applications",
    ];
    
    const RSS_URLS = [
        "/vacancies/auditions.rss",
        "/vacancies/techies.rss",
        "/vacancies/applications.rss",
    ];
    
    const TEXT_URLS = [
        "/vacancies/auditions.txt",
        "/vacancies/techies.txt",
        "/vacancies/applications.txt",
    ];
    
    /**
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;
    
    public function setUp()
    {
        $this->client = self::createClient(array('environment' => 'test'));
        
        //Generates database schema using in-memory SQLite database
        $this->client->getKernel()->getContainer()->get('acts_camdram_backend.database_tools')->resetDatabase();
    }
    
    public function testHtmlSuccessful()
    {
        foreach (self::HTML_URLS as $url)
        {
            $this->client->request('GET', $url);
            $response = $this->client->getResponse();
            
            $this->assertEquals(200, $response->getStatusCode(), "URL: $url");
            $this->assertContains('text/html', $response->headers->get('Content-Type'), "URL: $url");
            $this->assertContains('<body>', $response->getContent(), "URL: $url");
            $this->assertContains('</body>', $response->getContent(), "URL: $url");
        }
    }
    
    public function testTextSuccessful()
    {
        foreach (self::TEXT_URLS as $url)
        {
            $this->client->request('GET', $url);
            $response = $this->client->getResponse();
            
            $this->assertEquals(200, $response->getStatusCode(), "URL: $url");
            $this->assertContains('text/plain', $response->headers->get('Content-Type'), "URL: $url");
            $this->assertNotContains('<html>', $response->getContent(), "URL: $url");
        }
    }
    
    public function testRssSuccessful()
    {
        foreach (self::RSS_URLS as $url)
        {
            $this->client->request('GET', $url);
            $response = $this->client->getResponse();
            $this->assertEquals(200, $response->getStatusCode(), "URL: $url");
            $this->assertContains('application/rss+xml', $response->headers->get('Content-Type'), "URL: $url");
            $this->assertContains('</rss>', $response->getContent(), 'URL: $url');
        }
    }
    
}