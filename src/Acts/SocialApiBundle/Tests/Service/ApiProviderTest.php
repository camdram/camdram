<?php
namespace Acts\SocialApiBundle\Tests\Service;

use Acts\SocialApiBundle\Service\ApiProvider;
use Symfony\Component\DependencyInjection\Container;

class ApiProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApiProvider;
     */
    private $provider;

    private $container;

    public function setUp()
    {
        $this->container = $this->getMock('\Symfony\Component\DependencyInjection\Container');
        $this->provider = new ApiProvider($this->container, array('facebook', 'twitter'));
    }

    public function testConstructor()
    {
        $this->assertEquals(array('facebook', 'twitter'), $this->provider->getNames());
    }

    public function testExistsTrue()
    {
        $this->assertTrue($this->provider->exists('facebook'));
        $this->assertTrue($this->provider->exists('twitter'));
    }

    public function testExistsFalse()
    {
        $this->assertFalse($this->provider->exists('google'));
    }

    public function testGetInvalidService()
    {
        $this->container->expects($this->never())->method('get');
        $this->setExpectedException('InvalidArgumentException');

        $this->provider->get('google');
    }

    public function testGet()
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('acts.social_api.apis.facebook'))
            ->will($this->returnValue('api_placeholder'));

        $this->assertEquals('api_placeholder', $this->provider->get('facebook'));
    }
}