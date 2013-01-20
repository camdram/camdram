<?php
namespace Acts\CamdramBundle\Service\Search;

use FOQ\ElasticaBundle\Client as BaseClient;

class ElasticaClient extends BaseClient
{
    public function request($path, $method, $data = array(), array $query = array())
    {
        try {
            return parent::request($path, $method, $data, $query);
        } catch (\Elastica_Exception_Abstract $e) {
            return new \Elastica_Response('{"took":0,"timed_out":false,"hits":{"total":0,"max_score":0,"hits":[]}}');
        }
    }
}
