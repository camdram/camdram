<?php
namespace Acts\SocialApiBundle\Service;

use Symfony\Component\HttpFoundation\Request;

use Buzz\Message\Request as HttpRequest;

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

    public function isAuthenticated()
    {
        return !is_null($this->token);
    }

    abstract public function authenticateAsSelf();

    abstract public function authenticateWithRequest(Request $request, $redirectUri);

    abstract public function getLoginUrl($redirectUri);

    abstract public function canAuthenticateRequest(Request $request);

}