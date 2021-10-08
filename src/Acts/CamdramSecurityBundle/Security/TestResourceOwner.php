<?php
namespace Acts\CamdramSecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\RouterInterface;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse;
use OAuth2\Model\OAuth2Token;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use HWI\Bundle\OAuthBundle\OAuth\StateInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class TestResourceOwner
 *
 * This is a HWIOAuthBundle resource owner that does not call any external APIs.
 * Is is only enabled in the "test" environment for testing purposes.
 */
class TestResourceOwner implements ResourceOwnerInterface
{
    private $name;

    /** @var KernelInterface */
    private $kernel;

    /** @var RouterInterface */
    private $router;

    /** @var StateInterface */
    private $state;

    public function __construct(KernelInterface $kernel, RouterInterface $router)
    {
        $this->kernel = $kernel;
        $this->router = $router;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOption($name)
    {
        return false;
    }

    public function getAccessToken(Request $request, $redirectUri, array $extraParameters = array()) : array
    {
        return [
            'access_token' => $request->query->get('test-token')
        ];
    }

    public function addPaths(array $paths)
    {
    }

    public function refreshAccessToken($refreshToken, array $extraParameters = [])
    {
    }

    public function getAuthorizationUrl($redirect_uri, array $params = array())
    {
        return $this->router->generate("auth_test_login", ['redirect_uri' => $redirect_uri]);
    }

    public function handles(Request $request)
    {
        return $this->kernel->getEnvironment() == 'test' 
            && $request->query->has('test-token');
    }

    public function isCsrfTokenValid($csrfToken)
    {
        return true;
    }

    public function getUserInformation(array $token, array $extraParameters = array())
    {
        $access_token = $token['access_token'];
        $data = json_decode(base64_decode($access_token), true);

        $response = new PathUserResponse;
        $response->setData($data);
        $response->setResourceOwner($this);
        $response->setOAuthToken(new OAuthToken($access_token));
        $response->setPaths(['identifier' => 'identifier',
            'name'=> 'name',
            'email' => 'email']);

        return $response;
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }

    public function storeState(StateInterface $state = null)
    {
        $this->state = $state;
    }

    public function addStateParameter(string $key, string $value): void
    {

    }
}
