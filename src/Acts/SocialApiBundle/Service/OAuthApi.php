<?php
namespace Acts\SocialApiBundle\Service;

use Symfony\Component\HttpFoundation\Request;

use Buzz\Client\ClientInterface as HttpClientInterface,
    Buzz\Message\RequestInterface as HttpRequestInterface,
    Buzz\Message\MessageInterface as HttpMessageInterface,
    Buzz\Message\Request as HttpRequest,
    Buzz\Message\Response as HttpResponse;

use Acts\SocialApiBundle\Utils\Inflector,
    Acts\SocialApiBundle\Exception\InvalidApiMethodException,
    Acts\SocialApiBundle\Api\ApiResponse;

abstract class OAuthApi extends RestApi
{

    private $token = null;

    private $token_secret = null;

    public function getToken()
    {
        return $this->token;
    }

    public function getTokenSecret()
    {
        return $this->token_secret;
    }

    public function authenticateWithCredentials($token, $token_secret = null)
    {
        $this->token = $token;
        $this->token_secret = $token_secret;
    }

    abstract public function authenticateAsSelf();

    abstract public function authenticateWithRequest(Request $request, $redirectUri);

    abstract public function getLoginUrl($redirectUri);


}