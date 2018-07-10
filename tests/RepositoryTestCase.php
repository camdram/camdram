<?php

namespace Camdram\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class RepositoryTestCase extends KernelTestCase
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
        static::$kernel->getContainer()->get('acts_camdram_admin.database_tools')->resetDatabase();
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
