<?php
namespace Acts\CamdramSecurityBundle\Security\Service;

use Buzz\Client\ClientInterface as HttpClientInterface,
    Buzz\Message\RequestInterface as HttpRequestInterface,
    Buzz\Message\MessageInterface as HttpMessageInterface,
    Buzz\Message\Request as HttpRequest,
    Buzz\Message\Response as HttpResponse;

use Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Http\HttpUtils;

//use HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse;

/**
 * AbstractResourceOwner
 *
 * @author Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 * @author Alexander <iam.asm89@gmail.com>
 * @author Francisco Facioni <fran6co@gmail.com>
 */
abstract class AbstractService implements ServiceInterface
{
    protected $options = array();

    protected $api;

    public function __construct(HttpClientInterface $httpClient, HttpUtils $httpUtils, $name, array $options)
    {
        $this->options = array_merge($this->options, $options);

        $this->httpClient = $httpClient;
        $this->httpUtils  = $httpUtils;
        $this->name       = $name;

        $this->configure();
    }

    /**
     * Gives a chance for extending providers to customize stuff
     */
    public function configure()
    {

    }

    public function setApi($api)
    {
        $this->api = $api;
    }
    
}
