<?php
namespace Acts\CamdramBackendBundle\Test;
use Symfony\Bundle\FrameworkBundle\Client as BaseClient;

class Client extends BaseClient
{
    static protected $connection;
    protected $requested;

    protected function doRequest($request)
    {

        $this->injectConnection();
        return $this->kernel->handle($request);
    }

    protected function injectConnection()
    {
        if (null === self::$connection) {
            self::$connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        } else {
            $this->getContainer()->set('doctrine.dbal.default_connection', self::$connection);
        }
        $this->getContainer()->get('doctrine.orm.entity_manager')->clear();
    }
}