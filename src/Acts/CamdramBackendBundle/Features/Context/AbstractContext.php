<?php

namespace Acts\CamdramBackendBundle\Features\Context;

use Behat\Behat\Context\BehatContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Acts\CamdramBundle\Service\Time;

/**
 * Feature context.
 */
abstract class AbstractContext extends BehatContext implements KernelAwareInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @return \Behat\MinkExtension\Context\MinkContext
     */
    protected function getMinkContext()
    {
        return $this->getMainContext()->getSubcontext('mink');
    }

    protected function getDbTools()
    {
        return $this->kernel->getContainer()->get('acts_camdram_backend.database_tools');
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    protected function getEntityManager()
    {
        return $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @BeforeScenario
     */
    public function mockTime()
    {
        Time::mockDateTime(new \DateTime('2000-07-03 15:30:00'));
    }
}
