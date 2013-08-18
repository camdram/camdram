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

    protected $name;

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

    /**
     *
     * Performs an HTTP request
     *
     * @param string $url     The url to fetch
     * @param string $content The content of the request
     * @param array  $headers The headers of the request
     * @param string $method  The HTTP method to use
     *
     * @return string The response content
     */
    protected function httpRequest($url, $content = null, $headers = array(), $method = null)
    {
        if (null === $method) {
            $method = null === $content ? HttpRequestInterface::METHOD_GET : HttpRequestInterface::METHOD_POST;
        }

        $request  = new HttpRequest($method, $url);
        $response = new HttpResponse();

        $request->setHeaders($headers);
        $request->setContent($content);

        $this->httpClient->send($request, $response);

        return $response;
    }

    /**
     * Get the 'parsed' content based on the response headers.
     *
     * @param HttpMessageInterface $rawResponse
     *
     * @return mixed
     */
    protected function getResponseContent(HttpMessageInterface $rawResponse)
    {
        if (false !== strpos($rawResponse->getHeader('Content-Type'), 'application/json')) {
            $response = json_decode($rawResponse->getContent(), true);

        } else {
            parse_str($rawResponse->getContent(), $response);
        }

        return $response;
    }

    /**
     * Retrieve an option by name
     *
     * @param string $name The option name
     *
     * @return mixed The option value
     *
     * @throws \InvalidArgumentException When the option does not exist
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException(sprintf('Unknown option "%s"', $name));
        }

        return $this->options[$name];
    }

    /**
     * @param string $url
     * @param array  $parameters
     *
     * @return mixed
     */
//    abstract protected function doGetAccessTokenRequest($url, array $parameters = array());

    /**
     * @param string $url
     * @param array  $parameters
     *
     * @return mixed
     */
  //  abstract protected function doGetUserInformationRequest($url, array $parameters = array());

}
