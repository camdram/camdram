<?php
namespace Acts\SocialApiBundle\Service;

use Symfony\Component\HttpFoundation\Request;

class SimpleApi extends RestApi
{

    protected function authenticateRequest(&$url, &$method, &$params)
    {
        $params['key'] = $this->config['key'];
    }

}