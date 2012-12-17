<?php
namespace Acts\SocialApiBundle\Tests\Service;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

use Acts\SocialApiBundle\Service\OAuth1Api;
use Acts\SocialApiBundle\Utils\Inflector;
use Symfony\Component\HttpFoundation\Session\Session;

use Buzz\Message\Request as HttpRequest,
    Buzz\Message\Response as HttpResponse;

class OAuth1ApiTest extends \PHPUnit_Framework_TestCase
{

    private $httpClient;

    /**
     * @var \Acts\SocialApiBundle\Service\OAuth1Api;
     */
    private $api;

    private $session;

    private $config = array(
        'client_id' => 'iiiiiii',
        'client_secret' => 'ssssss',
        'base_url' => 'https://api.twitter.com/1.1',
        'access_token' => 'ooooooooo',
        'access_token_secret' => 'aaaaaaaaaaaa',
        'login_url' => 'https://api.twitter.com/oauth/authenticate',
        'paths' => array(
            'search' => array(
                'path' => '/users/search.json',
                'arguments' => array('q'),
                'requires_authentication' => true,
                'method' => 'GET',
                'response' => array('root' => null, 'map' => array('screen_name' => 'username'))
            )
        )
    );

    public function setUp()
    {
        $this->httpClient = $this->getMock('\Buzz\Client\Curl');
        $this->api = $this->getMockBuilder('\Acts\SocialApiBundle\Service\OAuth1Api')
            ->setMethods(array('httpRequest', 'generateNonce', 'getTimestamp', 'doRequestToken', 'doAccessToken'))
            ->setConstructorArgs(array($this->httpClient, new Inflector, 'twitter', $this->config))
            ->getMock();

        $this->api->expects($this->any())->method('generateNonce')
            ->will($this->returnValue('demo_nonce'));
        $this->api->expects($this->any())->method('getTimestamp')
            ->will($this->returnValue(1355713566));

        $this->session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');
        $this->api->setSession($this->session);
    }


    public function testConstructor()
    {
        $this->assertInstanceOf('Acts\SocialApiBundle\Service\OAuth1Api', $this->api);
    }

    public function testAuthenticateWithCredentials()
    {
        $this->api->authenticateWithCredentials('xxxx', 'yyyy');
        $this->assertEquals('xxxx', $this->api->getToken());
        $this->assertEquals('yyyy', $this->api->getTokenSecret());
    }

    public function testAuthenticateAsSelf()
    {
        $this->api->authenticateAsSelf();
        $this->assertEquals('ooooooooo', $this->api->getToken());
        $this->assertEquals('aaaaaaaaaaaa', $this->api->getTokenSecret());
    }

    public function testAuthenticateRequest()
    {
        $url = 'https://api.twitter.com/1.1/users/search.json';
        $method = 'GET';
        $params = array(
            'q' => 'blah',
            'oauth_consumer_key' => 'iiiiiii',
            'oauth_timestamp' => 1355713566,
            'oauth_nonce' => 'demo_nonce',
            'oauth_version' => '1.0',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => 'xxxx',
            'oauth_signature' => 'jtzrA40Mp57I/cjoFLXmuYWLdSo=',
        );

        $this->api->expects($this->once())->method('httpRequest')
            ->with($url, $method, $params)
            ->will($this->returnValue(array('test' => 'data')));

        $this->api->authenticateWithCredentials('xxxx', 'yyyy');
        $this->api->callMethod('search',array('blah'));
    }

    public function testGetLoginUrl()
    {
        $redirectUri = 'http://www.camdram.net/login/twitter';
        $token_response = array(
            'oauth_token' => 'Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik',
            'oauth_token_secret' => 'Kd75W4OQfb2oJTV0vzGzeXftVAwgMnEK9MumzYcM&',
        );

        $this->api->expects($this->once())->method('doRequestToken')
            ->with($redirectUri)
            ->will($this->returnValue($token_response));

        $url = $this->api->getLoginUrl($redirectUri);
        $this->assertEquals('https://api.twitter.com/oauth/authenticate?oauth_token=Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik', $url);
    }

    public function testGetLoginUrlProblem()
    {
        $redirectUri = 'http://www.camdram.net/login/twitter';
        $token_response = array(
            'oauth_problem' => 'Something went wrong',
        );

        $this->api->expects($this->once())->method('doRequestToken')
            ->with($redirectUri)
            ->will($this->returnValue($token_response));

        $this->setExpectedException('\Acts\SocialApiBundle\Exception\OAuthException');
        $url = $this->api->getLoginUrl($redirectUri);
    }

    public function testGetLoginUrlNoToken()
    {
        $redirectUri = 'http://www.camdram.net/login/twitter';
        $token_response = array(
            'error' => 'Something went wrong',
        );

        $this->api->expects($this->once())->method('doRequestToken')
            ->with($redirectUri)
            ->will($this->returnValue($token_response));

        $this->setExpectedException('\Acts\SocialApiBundle\Exception\OAuthException');
        $url = $this->api->getLoginUrl($redirectUri);
    }

    public function testAuthenticateWithRequest()
    {
        $this->session->expects($this->once())
            ->method('get')->with('twitter_oauth_token')
            ->will($this->returnValue(array(
            'oauth_token' => 'Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik',
            'oauth_token_secret' => 'Kd75W4OQfb2oJTV0vzGzeXftVAwgMnEK9MumzYcM&',
        )));


        $redirectUri = 'http://www.camdram.net/login/twitter';
        $verifier = 'apdhfklsjdfhSDFADuweof';
        $request = new Request(array('oauth_verifier' => $verifier));
        $token_response = array(
            'oauth_token' => '6253282-eWudHldSbIaelX7swmsiHImEL4KinwaGloHANdrY',
            'oauth_token_secret' => '2EEfA6BG3ly3sR3RjE0IBSnlQu4ZrUzPiYKmrkVU',
        );

        $this->api->expects($this->once())->method('doAccessToken')
            ->with($verifier)->will($this->returnValue($token_response));

        $this->api->authenticateWithRequest($request, $redirectUri);
        $this->assertEquals('6253282-eWudHldSbIaelX7swmsiHImEL4KinwaGloHANdrY', $this->api->getToken());
        $this->assertEquals('2EEfA6BG3ly3sR3RjE0IBSnlQu4ZrUzPiYKmrkVU', $this->api->getTokenSecret());
    }

    public function testAuthenticateWithRequestNoSessionToken()
    {
        $this->session->expects($this->once())
            ->method('get')->with('twitter_oauth_token')
            ->will($this->returnValue(null));


        $redirectUri = 'http://www.camdram.net/login/twitter';
        $verifier = 'apdhfklsjdfhSDFADuweof';
        $request = new Request(array('oauth_verifier' => $verifier));

        $this->api->expects($this->never())->method('doAccessToken');

        $this->setExpectedException('RuntimeException');
        $this->api->authenticateWithRequest($request, $redirectUri);
    }

    public function testAuthenticateWithRequestProblem()
    {
        $this->session->expects($this->once())
            ->method('get')->with('twitter_oauth_token')
            ->will($this->returnValue(array(
            'oauth_token' => 'Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik',
            'oauth_token_secret' => 'Kd75W4OQfb2oJTV0vzGzeXftVAwgMnEK9MumzYcM&',
        )));

        $redirectUri = 'http://www.camdram.net/login/twitter';
        $verifier = 'apdhfklsjdfhSDFADuweof';
        $request = new Request(array('oauth_verifier' => $verifier));
        $token_response = array(
            'oauth_problem' => 'Something went wrong',
        );

        $this->api->expects($this->once())->method('doAccessToken')
            ->with($verifier)->will($this->returnValue($token_response));

        $this->setExpectedException('\Acts\SocialApiBundle\Exception\OAuthException');
        $this->api->authenticateWithRequest($request, $redirectUri);
    }

    public function testAuthenticateWithRequestNoToken()
    {
        $this->session->expects($this->once())
            ->method('get')->with('twitter_oauth_token')
            ->will($this->returnValue(array(
            'oauth_token' => 'Z6eEdO8MOmk394WozF5oKyuAv855l4Mlqo7hhlSLik',
            'oauth_token_secret' => 'Kd75W4OQfb2oJTV0vzGzeXftVAwgMnEK9MumzYcM&',
        )));

        $redirectUri = 'http://www.camdram.net/login/twitter';
        $verifier = 'apdhfklsjdfhSDFADuweof';
        $request = new Request(array('oauth_verifier' => $verifier));
        $token_response = array(
            'error' => 'Something went wrong',
        );

        $this->api->expects($this->once())->method('doAccessToken')
            ->with($verifier)->will($this->returnValue($token_response));

        $this->setExpectedException('\Acts\SocialApiBundle\Exception\OAuthException');
        $this->api->authenticateWithRequest($request, $redirectUri);
    }

}