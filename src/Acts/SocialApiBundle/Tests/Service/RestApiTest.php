<?php
namespace Acts\SocialApiBundle\Tests\Service;

use Acts\SocialApiBundle\Service\ApiProvider;
use Symfony\Component\DependencyInjection\Container;

use Buzz\Client\Curl;

use Acts\SocialApiBundle\Service\RestApi;
use Acts\SocialApiBundle\Utils\Inflector;
use Acts\SocialApiBundle\Exception\InvalidApiMethodException;
use Acts\SocialApiBundle\Api\ApiResponse;

use Buzz\Message\Request as HttpRequest,
    Buzz\Message\Response as HttpResponse;

class RestApiTest extends \PHPUnit_Framework_TestCase
{

    private $httpClient;

    private $api;

    private $config = array(
        'base_url' => 'https://graph.facebook.com',
        'paths' => array(
            'search' => array(
                'path' => '/search',
                'arguments' => array('q', 'type'),
                'defaults' => array(),
                'url_has_params' => false,
                'requires_authentication' => true,
                'method' => 'GET',
                'response' => array('root' => null, 'map' => array())
            ),
            'current_user' => array(
                'path' => '/me',
                'arguments' => array(),
                'defaults' => array(),
                'url_has_params' => false,
                'requires_authentication' => true,
                'method' => 'GET',
                'response' => array('root' => null, 'map' => array())
            )
        )
    );

    public function setUp()
    {

        $this->httpClient = $this->getMock('\Buzz\Client\Curl');
        $this->api = new RestApi($this->httpClient, new Inflector, 'facebook', 'test_agent', $this->config);
    }


    public function testConstructor()
    {
        $this->assertInstanceOf('Acts\SocialApiBundle\Service\RestApi', $this->api);
    }

    public function testCallMethod()
    {
        $response = ApiResponse::factory(array('test' => 'data'));

        $api = $this->getMockBuilder('Acts\SocialApiBundle\Service\RestApi')
            ->setMethods(array('httpRequest'))
            ->setConstructorArgs(array($this->httpClient, new Inflector, 'facebook', 'test_agent', $this->config))
            ->getMock();
        $api->expects($this->once())->method('httpRequest')
            ->with('https://graph.facebook.com/search', 'GET', array('q' => 'blah', 'type' => 'page'))
            ->will($this->returnValue(array('test' => 'data')));

        $this->assertEquals($response, $api->callMethod('search', array('blah', 'page')));
    }

    public function testMagicCall()
    {
        $response = ApiResponse::factory(array('test' => 'data'));

        $api = $this->getMockBuilder('Acts\SocialApiBundle\Service\RestApi')
            ->setMethods(array('httpRequest'))
            ->setConstructorArgs(array($this->httpClient, new Inflector, 'facebook', 'test_agent', $this->config))
            ->getMock();
        $api->expects($this->once())->method('httpRequest')
            ->with('https://graph.facebook.com/search', 'GET', array('q' => 'blah', 'type' => 'page'))
            ->will($this->returnValue(array('test' => 'data')));

        $this->assertEquals($response, $api->doSearch('blah', 'page'));
    }

    public function testInvalidMethod()
    {
        $api = $this->getMockBuilder('Acts\SocialApiBundle\Service\RestApi')
            ->setMethods(array('httpRequest'))
            ->setConstructorArgs(array($this->httpClient, new Inflector, 'facebook', 'test_agent', $this->config))
            ->getMock();
        $api->expects($this->never())->method('httpRequest');

        try {
            $api->doInvalidMethod('blah');
        }
        catch (InvalidApiMethodException $e) {
            $this->assertEquals('facebook', $e->getApiName());
            $this->assertEquals('invalid_method', $e->getMethod());
        }
    }

    public function testZeroArguments()
    {
        $response = ApiResponse::factory(array('test' => 'data'));

        $api = $this->getMockBuilder('Acts\SocialApiBundle\Service\RestApi')
            ->setMethods(array('httpRequest'))
            ->setConstructorArgs(array($this->httpClient, new Inflector, 'facebook', 'test_agent', $this->config))
            ->getMock();
        $api->expects($this->once())->method('httpRequest')
            ->with('https://graph.facebook.com/me', 'GET', array())
            ->will($this->returnValue(array('test' => 'data')));

        $data = $api->doCurrentUser();
        $this->assertEquals($response, $data);
    }

    public function testMagicInvalidMethod()
    {
        $api = $this->getMockBuilder('Acts\SocialApiBundle\Service\RestApi')
            ->setMethods(array('httpRequest'))
            ->setConstructorArgs(array($this->httpClient, new Inflector, 'facebook', 'test_agent', $this->config))
            ->getMock();
        $api->expects($this->never())->method('httpRequest');
        $this->setExpectedException('\BadMethodCallException');

        $api->invalidMethodName('blah');
    }

    public function testCallAuthenticated()
    {
        $response = ApiResponse::factory(array('test' => 'data'));

        $api = $this->getMockBuilder('Acts\SocialApiBundle\Service\RestApi')
            ->setMethods(array('httpRequest', 'authenticateRequest'))
            ->setConstructorArgs(array($this->httpClient, new Inflector, 'facebook', 'test_agent', $this->config))
            ->getMock();
        $api->expects($this->once())->method('httpRequest')
            ->with('https://graph.facebook.com/search', 'GET', array('q' => 'blah', 'type' => 'page'))
            ->will($this->returnValue(array('test' => 'data')));
        $api->expects($this->once())->method('authenticateRequest')
            ->with('https://graph.facebook.com/search', 'GET', array('q' => 'blah', 'type' => 'page'));

        $this->assertEquals($response, $api->callMethod('search', array('blah', 'page')));
    }

    public function testCallNotAuthenticated()
    {
        $response = ApiResponse::factory(array('test' => 'data'));
        $this->config['paths']['search']['requires_authentication'] = false;

        $api = $this->getMockBuilder('Acts\SocialApiBundle\Service\RestApi')
            ->setMethods(array('httpRequest', 'authenticateRequest'))
            ->setConstructorArgs(array($this->httpClient, new Inflector, 'facebook', 'test_agent', $this->config))
            ->getMock();
        $api->expects($this->once())->method('httpRequest')
            ->with('https://graph.facebook.com/search', 'GET', array('q' => 'blah', 'type' => 'page'))
            ->will($this->returnValue(array('test' => 'data')));
        $api->expects($this->never())->method('authenticateRequest');

        $this->assertEquals($response, $api->callMethod('search', array('blah', 'page')));
    }

    public function testCallDifferentArgumentNumbers()
    {
        $response = ApiResponse::factory(array('test' => 'data'));
        $this->config['paths']['search']['requires_authentication'] = false;

        $api = $this->getMockBuilder('Acts\SocialApiBundle\Service\RestApi')
            ->setMethods(array('httpRequest', 'authenticateRequest'))
            ->setConstructorArgs(array($this->httpClient, new Inflector, 'facebook', 'test_agent', $this->config))
            ->getMock();
        $api->expects($this->once())->method('httpRequest')
            ->with('https://graph.facebook.com/search', 'GET', array('q' => 'blah'))
            ->will($this->returnValue(array('test' => 'data')));
        $api->expects($this->never())->method('authenticateRequest');

        $this->assertEquals($response, $api->callMethod('search', array('blah')));
    }

    public function testGet()
    {
        $request  = new HttpRequest('GET', 'https://graph.facebook.com/search?foo=bar');
        $request->setContent(array('foo' => 'bar'));
        $request->setHeaders(array('User-Agent' => 'test_agent'));

        $this->httpClient->expects($this->once())
            ->method('send')->with($request)->will($this->returnCallback(function($req, HttpResponse $resp) {
                $resp->setContent('{"test": "data"}');
                $resp->addHeader('Content-Type: application/json');
            }));


        $ret = $this->api->get('/search', array('foo' => 'bar'));
        $this->assertEquals(array('test' => 'data'), $ret);
    }

   /* public function testGetQueryStrResponse()
    {
        $request  = new HttpRequest('GET', 'https://graph.facebook.com/search?foo=bar');
        $request->setContent(array('foo' => 'bar'));
        $request->setHeaders(array('User-Agent' => 'test_agent'));

        $this->httpClient->expects($this->once())
            ->method('send')->with($request)->will($this->returnCallback(function($req, HttpResponse $resp) {
            $resp->setContent('test=data');
        }));


        $ret = $this->api->get('/search', array('foo' => 'bar'));
        $this->assertEquals(array('test' => 'data'), $ret);
    }

    public function testPost()
    {
        $request  = new HttpRequest('POST', 'https://graph.facebook.com/me/feed');
        $request->setContent(array('foo' => 'bar'));
        $request->setHeaders(array('User-Agent' => 'test_agent'));

        $this->httpClient->expects($this->once())
            ->method('send')->with($request)->will($this->returnCallback(function($req, $resp) {
                $resp->setContent('{"test": "data"}');
                $resp->addHeader('Content-Type: application/json');
            }));

        $this->assertEquals(array('test' => 'data'), $this->api->post('/me/feed', array('foo' => 'bar')));
    }

    public function testTransportError()
    {
        $request  = new HttpRequest('GET', 'https://graph.facebook.com/search?foo=bar');
        $request->setContent(array('foo' => 'bar'));
        $request->setHeaders(array('User-Agent' => 'test_agent'));

        $this->httpClient->expects($this->once())
            ->method('send')->with($request)
            ->will($this->throwException(new \RuntimeException("Cannot connect to host")));


        $this->setExpectedException('\Acts\SocialApiBundle\Exception\TransportException');
        $this->api->get('/search', array('foo' => 'bar'));
    }*/
}