<?php
namespace Acts\SocialApiBundle\Tests\Service;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

use Acts\SocialApiBundle\Service\OAuth2Api;
use Acts\SocialApiBundle\Utils\Inflector;

use Buzz\Message\Request as HttpRequest,
    Buzz\Message\Response as HttpResponse;

class OAuth2ApiTest extends \PHPUnit_Framework_TestCase
{

    private $httpClient;

    /**
     * @var \Acts\SocialApiBundle\Service\OAuth1Api;
     */
    private $api;

    private $config = array(
        'client_id' => 'iiiiiii',
        'client_secret' => 'ssssss',
        'base_url' => 'https://graph.facebook.com',
        'login_url' => 'https://www.facebook.com/dialog/oauth',
        'scope' => 'email',
        'paths' => array(
            'search' => array(
                'path' => '/search',
                'arguments' => array('q', 'type'),
                'requires_authentication' => true,
                'defaults' => array(),
                'url_has_params' => false,
                'method' => 'GET',
                'response' => array('root' => null, 'map' => array())
            )
        )
    );

    public function setUp()
    {
        $this->httpClient = $this->getMock('\Buzz\Client\Curl');
        $this->api = $this->getMockBuilder('\Acts\SocialApiBundle\Service\OAuth2Api')
            ->setMethods(array('httpRequest', 'doRequestToken', 'doAccessToken'))
            ->setConstructorArgs(array($this->httpClient, new Inflector, 'facebook', 'test_agent', $this->config))
            ->getMock();
    }


    public function testConstructor()
    {
        $this->assertInstanceOf('Acts\SocialApiBundle\Service\OAuth2Api', $this->api);
    }

    public function testAuthenticateWithCredentials()
    {
        $this->api->authenticateWithCredentials('xxxx');
        $this->assertEquals('xxxx', $this->api->getToken());
        $this->assertNull($this->api->getTokenSecret());
    }

    public function testAuthenticateAsSelf()
    {
        $token_response = array(
            'access_token' => 'pasjdfhkjlashkfj892y3e89f',
        );

        $this->api->expects($this->once())->method('doAccessToken')
            ->with($this->config['client_id'], $this->config['client_secret'], null,
            'client_credentials')->will($this->returnValue($token_response));

        $this->api->authenticateAsSelf();
        $this->assertEquals('pasjdfhkjlashkfj892y3e89f', $this->api->getToken());
    }

    public function testAuthenticateRequest()
    {
        $url = 'https://graph.facebook.com/search';
        $method = 'GET';
        $params = array(
            'q' => 'blah',
            'access_token' => 'xxxx',
        );

        $this->api->expects($this->once())->method('httpRequest')
            ->with($url, $method, $params)
            ->will($this->returnValue(array('test' => 'data')));

        $this->api->authenticateWithCredentials('xxxx');
        $this->api->callMethod('search',array('blah'));
    }

    public function testGetLoginUrl()
    {
        $redirectUri = 'http://www.camdram.net/login/facebook';

        $url = $this->api->getLoginUrl($redirectUri);
        $this->assertEquals('https://www.facebook.com/dialog/oauth?response_type=code&client_id=iiiiiii'
                .'&scope=email&redirect_uri=http%3A%2F%2Fwww.camdram.net%2Flogin%2Ffacebook', $url);
    }

    public function testAuthenticateWithRequest()
    {
        $redirectUri = 'http://www.camdram.net/login/facebook';
        $code = 'ccccc';
        $request = new Request(array('code' => $code));
        $token_response = array(
            'access_token' => 'pasjdfhkjlashkfj892y3e89f',
        );

        $this->api->expects($this->once())->method('doAccessToken')
            ->with($this->config['client_id'], $this->config['client_secret'], $code,
                    'authorization_code', $redirectUri)->will($this->returnValue($token_response));

        $this->api->authenticateWithRequest($request, $redirectUri);
        $this->assertEquals('pasjdfhkjlashkfj892y3e89f', $this->api->getToken());
    }

    public function testAuthenticateWithRequestInvalid()
    {
        $redirectUri = 'http://www.camdram.net/login/facebook';
        $code = 'ccccc';
        $request = new Request(array('code' => $code));
        $token_response = array(
            'error' => 'Something\'s gone wrong',
        );

        $this->api->expects($this->once())->method('doAccessToken')
            ->with($this->config['client_id'], $this->config['client_secret'], $code,
            'authorization_code', $redirectUri)->will($this->returnValue($token_response));

        $this->setExpectedException('\Acts\SocialApiBundle\Exception\OAuthException');
        $this->api->authenticateWithRequest($request, $redirectUri);
        $this->asserNull($this->api->getToken());
    }

}