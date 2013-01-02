<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Dbal;

use Doctrine\Common\PropertyChangedListener;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Exception\ConcurrentModificationException;
use Symfony\Component\Security\Acl\Model\AclCacheInterface;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Acl\Model\PermissionGrantingStrategyInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use Symfony\Component\Security\Acl\Dbal\AclProvider;

/**
 * An implementation of the MutableAclProviderInterface using Doctrine DBAL.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class AclListProvider extends AclProvider
{

    /**
     * Get the entities Ids for the className that match the given role & mask
     *
     * @param string $className
     * @param string $roles
     * @param integer $mask
     *
     * @return bool|array|string - True if its allowed to all entities, false if its not
     *          allowed, array or string depending on $asString parameter.
     */
    public function getAllowedEntitiesIds($className, array $roles, $mask)
    {

        // Check for class-level global permission (its a very similar query to the one
        // posted above
        // If there is a class-level grant permission, then do not query object-level
        /*if ($this->maskMatchesRoleForClass($className, $roles, $mask)) {
            return true;
        }*/

        // Query the database for ACE's matching the mask for the given roles
        $sql = $this->getEntitiesIdsMatchingRoleMaskSql($className, $roles, $mask);
        $ids = $this->connection->executeQuery($sql)->fetchAll(\PDO::FETCH_COLUMN);

        return $ids;
    }

    private function getEntitiesIdsMatchingRoleMaskSql($className, array $roles, $requiredMask)
    {
        $rolesSql = array();
        foreach($roles as $role) {
            $rolesSql[] = 's.identifier = ' . $this->connection->quote($role);
        }
        $rolesSql =  '(' . implode(' OR ', $rolesSql) . ')';

        $sql = <<<SELECTCLAUSE
        SELECT
            oid.object_identifier
        FROM
            {$this->options['entry_table_name']} e
        JOIN
            {$this->options['oid_table_name']} oid ON (
            oid.id = e.object_identity_id
        )
        JOIN {$this->options['sid_table_name']} s ON (
            s.id = e.security_identity_id
        )
        JOIN {$this->options['class_table_name']} class ON (
            class.id = e.class_id
        )
        WHERE
            {$this->connection->getDatabasePlatform()->getIsNotNullExpression('e.object_identity_id')} AND
            (e.mask & %d) AND
            $rolesSql AND
            class.class_type = %s
       GROUP BY
            oid.object_identifier
SELECTCLAUSE;

        return sprintf(
            $sql,
            $requiredMask,
            $this->connection->quote($className)
        );

    }
}
