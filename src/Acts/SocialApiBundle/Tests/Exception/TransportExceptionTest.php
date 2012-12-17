<?php
namespace Acts\SocialApiBundle\Tests\Exception;

use Acts\SocialApiBundle\Exception\TransportException;

class TransportExceptionTest extends \PHPUnit_Framework_TestCase
{

   public function testUrl()
   {
       $url = 'http://graph.facebook.com/search';

       $e = new TransportException('Cannot connect to host');
       $e->setUrl($url);
       $this->assertEquals($url, $e->getUrl());
   }

    public function testApiName()
    {
        $api_name = 'facebook';

        $e = new TransportException('Cannot connect to host');
        $e->setApiName($api_name);
        $this->assertEquals($api_name, $e->getApiName());
    }

}
