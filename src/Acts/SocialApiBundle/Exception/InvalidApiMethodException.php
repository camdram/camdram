<?php
namespace Acts\SocialApiBundle\Exception;

class InvalidApiMethodException extends SocialApiException
{
    /**
     * The api being called
     *
     * @var string
     */
    private $api_name;

    /**
     * The method name being called
     *
     * @var string
     */
    private $method;

    public function __construct($api_name, $method)
    {
        $this->api_name = $api_name;
        $this->method = $method;
        parent::__construct('The method "'.$method.'" could not be found for the '.ucfirst($api_name).' API');
    }

    public function getApiName()
    {
        return $this->api_name;
    }

    public function getMethod()
    {
        return $this->method;
    }
}