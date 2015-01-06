<?php

namespace Acts\CamdramApiBundle\Features\Context;

use Acts\CamdramApiBundle\Entity\ExternalApp;
use Acts\CamdramBackendBundle\Features\Context\AbstractContext;
use Acts\CamdramBackendBundle\Features\Context\EntityContext;
use Acts\CamdramBackendBundle\Features\Context\SymfonyContext;
use Acts\CamdramBackendBundle\Features\Context\UserContext;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Mink\Driver\BrowserKitDriver;
use Guzzle\Http\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;


/**
 * Feature context.
 */
class FeatureContext extends BehatContext
{


    public function __construct(array $params)
    {
        $this->useContext('mink', new MinkContext());
        $this->useContext('symfony', new SymfonyContext());
        $this->useContext('entity', new EntityContext());
        $this->useContext('rest', new RestContext());
    }

}
