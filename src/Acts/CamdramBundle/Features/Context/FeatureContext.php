<?php

namespace Acts\CamdramBundle\Features\Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\BehatContext;

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
