<?php
namespace Acts\SocialApiBundle\Tests\Exception;

use Acts\SocialApiBundle\Exception\InvalidApiMethodException;

class InvalidApiMethodExceptionTest extends \PHPUnit_Framework_TestCase
{

   public function testConstructor()
   {
       $e = new InvalidApiMethodException('facebook', 'invalid_method');
       $this->assertEquals('facebook' , $e->getApiName());
       $this->assertEquals('invalid_method', $e->getMethod());
   }

}
