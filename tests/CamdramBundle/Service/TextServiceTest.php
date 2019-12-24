<?php

namespace Camdram\Tests\CamdramBundle\Controller;

use Acts\CamdramBundle\Service\TextService;
use PHPUnit\Framework\TestCase;

class TextServiceTest extends TestCase
{
    /**
     * @var \Acts\CamdramBundle\Service\TextService
     */
    private $textService;

    public function setUp(): void
    {
        $this->textService = new TextService();
    }

    public function testTruncate()
    {
        $text = 'A quick brown fox';

        $this->assertEquals('A quick br…', $this->textService->truncate($text, 10));
        $this->assertEquals('A quick brown fo…', $this->textService->truncate($text, 16));
        $this->assertEquals('A quick brown fox', $this->textService->truncate($text, 17));
        $this->assertEquals('A quick brown fox', $this->textService->truncate($text, 20));
    }

    public function testStripNewLines()
    {
        $this->assertEquals('A quick brown fox', $this->textService->stripNewLines("A quick\r brown fox"));
        $this->assertEquals('A quick brown fox', $this->textService->stripNewLines("A quick\n brown fox"));
        $this->assertEquals('A quick brown fox', $this->textService->stripNewLines("A quick\r\n brown fox"));
    }

    public function testDetectLinks_Urls()
    {
        $this->assertEquals(
            'The website <a href="http://www.camdram.net/" rel="ext" target="_blank">http://www.camdram.net/</a> is great',
            $this->textService->detectLinks('The website http://www.camdram.net/ is great')
        );
        $this->assertEquals(
            'The website <a href="http://www.camdram.net" rel="ext" target="_blank">http://www.camdram.net</a> is great',
            $this->textService->detectLinks('The website http://www.camdram.net is great')
        );
        $this->assertEquals(
            'The website <a href="http://www.camdram.net" rel="ext" target="_blank">www.camdram.net</a> is great',
            $this->textService->detectLinks('The website www.camdram.net is great')
        );
        $this->assertEquals(
            'The website <a href="https://www.camdram.net" rel="ext" target="_blank">https://www.camdram.net</a> is great',
            $this->textService->detectLinks('The website https://www.camdram.net is great')
        );
        $this->assertEquals(
            'The website <a href="http://www.camdram.net" rel="ext" target="_blank">www.camdram.net</a>.',
            $this->textService->detectLinks('The website www.camdram.net.')
        );
    }

    public function testDetectLinks_Emails()
    {
        $this->assertEquals(
            'The email <a href="mailto:support@camdram.net">support@camdram.net</a> can be used for support',
            $this->textService->detectLinks('The email support@camdram.net can be used for support')
        );
    }

    public function testConvertMarkdown_Links()
    {
        $this->assertEquals(
            '<p>The website <a href="http://www.camdram.net">www.camdram.net</a> is great</p>',
            $this->textService->convertMarkdown('The website [L:www.camdram.net] is great')
        );
        $this->assertEquals(
            '<p>The website <a href="http://www.camdram.net">http://www.camdram.net</a> is great</p>',
            $this->textService->convertMarkdown('The website [L:http://www.camdram.net] is great')
        );
        $this->assertEquals(
            '<p>The website <a href="https://www.camdram.net">Camdram</a> is great</p>',
            $this->textService->convertMarkdown('The website [L:https://www.camdram.net;Camdram] is great')
        );
        $this->assertEquals(
            '<p>The website <a href="http://www.camdram.net">Camdram</a> is great</p>',
            $this->textService->convertMarkdown('The website [L:www.camdram.net;Camdram] is great')
        );
    }

    public function testConvertMarkdown_Emails()
    {
        $this->assertEquals(
            '<p>The email <a href="mailto:support@camdram.net">support@camdram.net</a> can be used for support</p>',
            $this->textService->convertMarkdown('The email [E:support@camdram.net] can be used for support')
        );
        $this->assertEquals(
            '<p>The <a href="mailto:support@camdram.net">support email</a> can be used for support</p>',
            $this->textService->convertMarkdown('The [E:support@camdram.net;support email] can be used for support')
        );
        $this->assertEquals(
            '<p>The email <a href="mailto:support@camdram.net">support@camdram.net</a> can be used for support</p>',
            $this->textService->convertMarkdown('The email [L:mailto:support@camdram.net] can be used for support')
        );
        $this->assertEquals(
            '<p>The <a href="mailto:support@camdram.net">support email</a> can be used for support</p>',
            $this->textService->convertMarkdown('The [L:mailto:support@camdram.net;support email] can be used for support')
        );
    }

    public function testConvertMarkdown_StripTags()
    {
        $this->assertEquals(
            '<p><strong>Hello</strong> <em>world</em></p>',
            $this->textService->convertMarkdown('<b>Hello</b> <i>world</i>')
        );
        $this->assertEquals(
            '<p>Hello world</p>',
            $this->textService->convertMarkdown('<script>Hello</script> <html>world</html>')
        );
    }

    public function testStripMarkDown_Tags()
    {
        $this->assertEquals('Hello world', $this->textService->stripMarkdown('<b>Hello</b> <i>world</i>'));
        $this->assertEquals('Hello  world', $this->textService->stripMarkdown('Hello <br /> world'));
    }

    public function testStripMarkdown_Links()
    {
        $this->assertEquals(
            'The website www.camdram.net is great',
            $this->textService->stripMarkdown('The website [L:www.camdram.net] is great')
        );
        $this->assertEquals(
            'The website Camdram is great',
            $this->textService->stripMarkdown('The website [L:www.camdram.net;Camdram] is great')
        );
    }

    public function testStripMarkdown_Emails()
    {
        $this->assertEquals(
            'support@camdram.net can be used for support',
            $this->textService->stripMarkdown('[E:support@camdram.net] can be used for support')
        );
        $this->assertEquals(
            'The support email can be used for support',
            $this->textService->stripMarkdown('The [E:support@camdram.net;support email] can be used for support')
        );
    }
}
