<?php
namespace Acts\ExternalLoginBundle\Security\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class TestService implements ServiceInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    protected $mock_service;

    public function __construct(RouterInterface $router, $environment, $mock_service) {
        if ($environment == 'prod') {
            throw new \InvalidArgumentException("The 'test' external login service cannot be used in the production environemnt");
        }

        $this->router = $router;
        $this->mock_service = $mock_service;
    }

    public function getName()
    {
        return $this->mock_service;
    }

    public function getAccessToken(Request $request, $redirectUri, array $extraParameters = array())
    {
        return array(
            'id' => $request->get('id'),
            'username' => $request->get('username'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'picture' => $request->get('picture'),
        );
    }

    public function getAuthorizationUrl($redirect_uri, array $extraParameters = array())
    {
        return $this->router->generate('acts_external_login_test_login', array('service' => $this->mock_service));
    }

    public function handles(Request $request)
    {
        return $request->query->has('username');
    }

    public function getUserInfo($token = null)
    {
        return $token;
    }

}