<?php
namespace Acts\SocialApiBundle\Exception;

class TransportException extends SocialApiException
{

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $api_name;


    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getApiName()
    {
        return $this->api_name;
    }

    public function setApiName($api_name)
    {
        $this->api_name = $api_name;
    }

}