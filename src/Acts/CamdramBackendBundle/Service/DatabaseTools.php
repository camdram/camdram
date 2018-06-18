<?php

namespace Acts\CamdramBackendBundle\Service;

use Doctrine\ORM\EntityManager;

class DatabaseTools
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function resetDatabase()
    {
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->dropDatabase();
        $tool->createSchema($metadatas);
    }
}
