<?php
namespace Acts\CamdramBackendBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class RepositoryTestCase extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    protected $repository;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        static::$kernel->getContainer()->get('acts_camdram_backend.database_tools')->resetDatabase();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->clear();
    }
}