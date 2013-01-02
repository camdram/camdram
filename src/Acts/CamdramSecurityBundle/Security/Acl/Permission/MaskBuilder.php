<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Permission;

class MaskBuilder extends \Symfony\Component\Security\Acl\Permission\MaskBuilder
{
    const MASK_SHOW_VIEW         = 256;          // 1 << 8
    const MASK_SHOW_CREATE       = 512;          // 1 << 9
    const MASK_SHOW_EDIT         = 1024;         // 1 << 10
    const MASK_SHOW_DELETE       = 2048;         // 1 << 11
    const MASK_SHOW_UNDELETE     = 4096;         // 1 << 12
    const MASK_SHOW_OPERATOR     = 8192;         // 1 << 13
    const MASK_SHOW_MASTER       = 16384;        // 1 << 14
    const MASK_SHOW_OWNER        = 32768;        // 1 << 15

    const CODE_SHOW_VIEW         = 'SV';
    const CODE_SHOW_CREATE       = 'SC';
    const CODE_SHOW_EDIT         = 'SE';
    const CODE_SHOW_DELETE       = 'SD';
    const CODE_SHOW_UNDELETE     = 'SU';
    const CODE_SHOW_OPERATOR     = 'SO';
    const CODE_SHOW_MASTER       = 'SM';
    const CODE_SHOW_OWNER        = 'SN';

}
