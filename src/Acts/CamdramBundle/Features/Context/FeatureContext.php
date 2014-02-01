<?php

namespace Acts\CamdramBundle\Features\Context;

use Acts\CamdramBackendBundle\Features\Context\EntityContext;
use Acts\CamdramBackendBundle\Features\Context\SymfonyContext;
use Acts\CamdramBackendBundle\Features\Context\UserContext;
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
