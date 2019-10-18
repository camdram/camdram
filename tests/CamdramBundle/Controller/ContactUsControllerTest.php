<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactUsControllerTest extends WebTestCase
{
    public function testSendMessage()
    {
        $client = static::createClient(['environment' => 'test']);
        $crawler = $client->request("GET", "/contact-us");

        $form = $crawler->selectButton('Send')->form();
        $form->setValues([
            'contact_us[name]' => 'John Smith',
            'contact_us[email]' => 'john@domain.com',
            'contact_us[subject]' => 'Test Message',
            'contact_us[message]' => 'Lorem ipsum'
        ]);
        $client->enableProfiler(); //Collects data about e-mails sent during request
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/contact-us/sent'));

        // check that an email was sent
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount());

        $message = $mailCollector->getMessages()[0];
        $this->assertStringContainsString('Test Message', $message->getSubject());
        $this->assertSame('john@domain.com', key($message->getReplyTo()));
        $recipient = $client->getKernel()->getContainer()->getParameter('support_email_address');
        $this->assertSame($recipient, key($message->getFrom()));
        $this->assertSame($recipient, key($message->getTo()));
        $this->assertStringContainsString('Lorem ipsum', $message->getBody());
    }

}
