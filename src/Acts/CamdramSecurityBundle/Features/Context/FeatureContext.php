<?php

namespace Acts\CamdramSecurityBundle\Features\Context;

use Acts\CamdramBackendBundle\Features\Context\EntityContext;
use Acts\CamdramBackendBundle\Features\Context\SymfonyContext;
use Acts\CamdramBackendBundle\Features\Context\UserContext;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Event\ScenarioEvent;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Feature context.
 */
class FeatureContext extends BehatContext
{
    public function __construct(array $params) {
        $this->useContext('mink', new MinkContext());
        $this->useContext('users', new UserContext());
        $this->useContext('symfony', new SymfonyContext());
        $this->useContext('entity', new EntityContext());
    }

    /**
     * @return \Behat\MinkExtension\Context\MinkContext
     */
    private function getMinkContext()
    {
        return $this->getMainContext()->getSubcontext('mink');
    }

}
