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

    public function truncate($entity_name)
    {
        $this->setForeignKeyChecks(false);
        $metadata = $this->em->getMetadataFactory()->getMetadataFor($entity_name);
        $platform = $this->em->getConnection()->getDatabasePlatform();
        $this->em->getConnection()->executeUpdate("TRUNCATE " . $metadata->getQuotedTableName($platform));
        $this->setForeignKeyChecks(true);
    }

    protected function setForeignKeyChecks($val)
    {
        $val = (int) $val;
        $this->em->getConnection()->exec("SET FOREIGN_KEY_CHECKS=$val;");
    }

}