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
    Acts\SocialApiBundle\Api\ApiResponse,
    Acts\SocialApiBundle\Exception\TransportException,
    Acts\SocialApiBundle\Exception\ApiException;

class RestApi
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Buzz\Client\ClientInterface
     */
    private $httpClient;

    /**
     * @var \Acts\SocialApiBundle\Utils\Inflector
     */
    private $inflector;

    /**
     * @param \Buzz\Client\ClientInterface $httpClient
     * @param \Acts\SocialApiBundle\Utils\Inflector $inflector
     * @param $name
     * @param array $config
     */
    public function __construct(HttpClientInterface $httpClient, Inflector $inflector, $name, array $config)
    {
        $this->httpClient = $httpClient;
        $this->inflector = $inflector;
        $this->name = $name;
        $this->config = $config;
    }

    public function get($uri, $params)
    {
        $url = $this->config['base_url'].$uri;
        return $this->httpRequest($url, 'GET', $params);
    }

    public function post($uri, $params)
    {
        $url = $this->config['base_url'].$uri;
        return $this->httpRequest($url,  'POST', $params);
    }

    public function getName()
    {
        return $this->name;
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
    protected function httpRequest($url, $method = HttpRequestInterface::METHOD_GET, $content = null, $headers = array())
    {
        if ($method == HttpRequestInterface::METHOD_GET) {
            $url .= '?'.http_build_query($content);
        }

        $request  = new HttpRequest($method, $url);
        $response = new HttpResponse();

        $request->setHeaders($headers);
        $request->setContent($content);

        try {
            $this->httpClient->send($request, $response);
        }
        catch (\RuntimeException $e) {
            $e = new TransportException(sprintf('Cannot connect to %s', ucfirst($this->getName())), $e->getCode(), $e);
            $e->setUrl($url);
            $e->setApiName(ucfirst($this->getName()));
            throw $e;
        }

        if (false !== strpos($response->getHeader('Content-Type'), 'text/javascript')
                || false !== strpos($response->getHeader('Content-Type'), 'application/json')) {
            $response = json_decode($response->getContent(), true);
        }
        else {
            parse_str($response->getContent(), $response);
        }

        return $response;
    }

    protected function authenticateRequest(&$url, &$method, &$params)
    {
        //do nothing by default
    }

    public function callMethod($name, $arguments)
    {
        if (!isset($this->config['paths'][$name])) {
            throw new InvalidApiMethodException($this->getName(), $name);
        }

        $config = $this->config['paths'][$name];

        $keys = $config['arguments'];
        $num_args = min(count($arguments), count($keys));
        if ($num_args > 0) {
            $keys = array_splice($keys,0,$num_args);
            $arguments = array_splice($arguments,0,$num_args);
            $params = array_combine($keys, $arguments);
        }
        else {
            $params = array();
        }


        $url = $this->config['base_url'].$config['path'];

        if ($config['requires_authentication'] == true) {
            $this->authenticateRequest($url, $config['method'], $params);
        }
        $response = $this->httpRequest($url, $config['method'], $params);

        if (isset($response['errors'])) {
            $error = current($response['errors']);
            throw new ApiException($error['message'], $error['code'], ucfirst($this->getName()));
        }

        return ApiResponse::factory($response, $this->config['paths'][$name]['response']);
    }

    public function __call($name, $arguments) {
        if (preg_match('/^do(.*)$/',$name, $matches)) {
            $method = $this->inflector->underscore($matches[1]);
            return $this->callMethod($method, $arguments);
        }
        throw new  \BadMethodCallException('The method "'.$name.'" does not exist');
    }


}