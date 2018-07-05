<?php

namespace Acts\CamdramSecurityBundle\Features\Context;

use Acts\CamdramBundle\Features\Context\EntityContext;
use Acts\CamdramBundle\Features\Context\SymfonyContext;
use Acts\CamdramBundle\Features\Context\UserContext;
use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Feature context.
 */
class FeatureContext extends BehatContext
{
    public function __construct(array $params)
    {
        $this->useContext('mink', new MinkContext());
        $this->useContext('users', new UserContext());
        $this->useContext('symfony', new SymfonyContext());
        $this->useContext('entity', new EntityContext());
    }
}
