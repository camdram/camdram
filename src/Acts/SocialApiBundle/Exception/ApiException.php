<?php
namespace Acts\SocialApiBundle\Exception;

class ApiException extends SocialApiException
{

    /**
     * @var string
     */
    private $api_name;

    /**
     * @var string
     */
    private $api_message;

    public function __construct($message, $code, $api_name)
    {
        $this->api_message = $message;

        $message = $api_name.' returned an error: '.$message;
        parent::__construct($message, $code);
    }

    public function getApiName()
    {
        return $this->api_name;
    }

    public function getApiMessage()
    {
        return $this->api_message;
    }
}