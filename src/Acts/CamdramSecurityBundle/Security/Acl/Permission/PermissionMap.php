<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Permission;

use Symfony\Component\Security\Acl\Permission\BasicPermissionMap;

class PermissionMap extends BasicPermissionMap
{
    const PERMISSION_SHOW_VIEW        = 'SHOW_VIEW';
    const PERMISSION_SHOW_EDIT        = 'SHOW_EDIT';
    const PERMISSION_SHOW_CREATE      = 'SHOW_CREATE';
    const PERMISSION_SHOW_DELETE      = 'SHOW_DELETE';
    const PERMISSION_SHOW_UNDELETE    = 'SHOW_UNDELETE';
    const PERMISSION_SHOW_OPERATOR    = 'SHOW_OPERATOR';
    const PERMISSION_SHOW_MASTER      = 'SHOW_MASTER';
    const PERMISSION_SHOW_OWNER       = 'SHOW_OWNER';

    private $map = array(
        self::PERMISSION_SHOW_VIEW => array(
            MaskBuilder::MASK_VIEW,
            MaskBuilder::MASK_EDIT,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_SHOW_EDIT => array(
            MaskBuilder::MASK_EDIT,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_SHOW_CREATE => array(
            MaskBuilder::MASK_CREATE,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_SHOW_DELETE => array(
            MaskBuilder::MASK_DELETE,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_SHOW_UNDELETE => array(
            MaskBuilder::MASK_UNDELETE,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_SHOW_OPERATOR => array(
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_SHOW_MASTER => array(
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        ),

        self::PERMISSION_SHOW_OWNER => array(
            MaskBuilder::MASK_OWNER,
        ),
    );

    /**
     * {@inheritDoc}
     */
    public function getMasks($permission, $object)
    {
        if ($result = parent::getMasks($permission, $object)) {
            return $result;
        }
        else {
            if (!isset($this->map[$permission])) {
                return null;
            }

            return $this->map[$permission];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function contains($permission)
    {
        return isset($this->map[$permission]) || !is_null(parent::getMasks($permission, NULL));
    }

    public function showToOwnerPermissions($permission)
    {
        var_dump($permission);
        $mask = $this->getMasks($permission, null);
        var_dump($mask);die();
    }
}
