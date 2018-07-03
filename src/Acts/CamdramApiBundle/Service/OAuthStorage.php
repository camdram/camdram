<?php
namespace Acts\CamdramApiBundle\Service;

use Acts\CamdramApiBundle\Entity\ExternalApp;
use OAuth2\Model\IOAuth2Client;

class OAuthStorage extends \FOS\OAuthServerBundle\Storage\OAuthStorage
{
    public function checkClientCredentialsGrant(IOAuth2Client $client, $client_secret)
    {
        if ($result = parent::checkClientCredentialsGrant($client, $client_secret)) {
            if ($client instanceof ExternalApp) {
                return array(
                    'data' => $client->getUser()
                );
            }
        }
        return $result;
    }
}
