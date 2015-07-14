<?php

namespace Acts\CamdramBackendBundle\Test\Service;

use Acts\CamdramBackendBundle\Service\EmailParser;

class EmailParserTest extends \PHPUnit_Framework_TestCase
{
    private $msg = "Blah blah blah\n\nRegards, John";

    public function testPlainEmail()
    {
        $txt = <<<EOT
Date: Sat, 29 Nov 2014 16:41:40 +0000
From: "camdram.net" <websupport@camdram.net>
To: websupport@camdram.net
MIME-Version: 1.0
Content-Type: text/plain; charset=utf-8
Content-Transfer-Encoding: quoted-printable

Blah blah blah

Regards, John
EOT;
        $parser = new EmailParser($txt);
        $this->assertEquals($this->msg, $parser->getTextPart());
    }

    public function testMultiPartEmail()
    {
        $txt = <<<EOT
Date: Sun, 30 Nov 2014 03:31:55 -0800
From: testuser@gmail.com
To: websupport@camdram.net
Subject: Test Email
Mime-Version: 1.0
Content-Type: multipart/alternative;
 boundary="--==_mimepart_547b002b7dad5_1fe53ff3bda0b2c024696c";
 charset=UTF-8
Content-Transfer-Encoding: 7bit

----==_mimepart_547b002b7dad5_1fe53ff3bda0b2c024696c
Content-Type: text/plain;
 charset=UTF-8
Content-Transfer-Encoding: 7bit

Blah blah blah

Regards, John
----==_mimepart_547b002b7dad5_1fe53ff3bda0b2c024696c
Content-Type: text/html;
 charset=UTF-8
Content-Transfer-Encoding: 7bit

<p>Blah blah blah</p>

<p>Regards, John</p>
----==_mimepart_547b002b7dad5_1fe53ff3bda0b2c024696c--
EOT;
        $parser = new EmailParser($txt);
        $this->assertContains($this->msg, $parser->getTextPart());
    }

    public function testbase64Email()
    {
        $txt = <<<EOT
Date: Sun, 30 Nov 2014 03:31:55 -0800
From: testuser@gmail.com
To: websupport@camdram.net
Subject: Test email
Mime-Version: 1.0
Content-Type: multipart/alternative;
 boundary="_BA30C081-D84C-4E2A-AB8C-105C9BB97412_";
 charset=UTF-8
Content-Transfer-Encoding: 7bit

--_BA30C081-D84C-4E2A-AB8C-105C9BB97412_
Content-Transfer-Encoding: base64
Content-Type: text/plain; charset="utf-8"

QmxhaCBibGFoIGJsYWgKClJlZ2FyZHMsIEpvaG4=

--_BA30C081-D84C-4E2A-AB8C-105C9BB97412_--
EOT;
        $parser = new EmailParser($txt);
        $this->assertContains($this->msg, $parser->getTextPart());
    }
}
