<?php
namespace Acts\CamdramSecurityBundle\Twig\Extension;

use Acts\CamdramSecurityBundle\Security\SecurityUtils;

class CamdramSecurityExtension extends \Twig_Extension
{
    /**
     * @var SecurityUtils
     */
    protected $utils;

    /**
     * @param OAuthHelper $helper
     */
    public function __construct(SecurityUtils $utils)
    {
        $this->utils = $utils;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'camdram_security_services' => new \Twig_Function_Method($this, 'getServices'),
            'acl_entries' => new \Twig_Function_Method($this, 'getAclEntries'),
            'is_granted' => new \Twig_Function_Method($this, 'isGranted'),
            'is_owner' => new \Twig_Function_Method($this, 'isOwner'),
            'has_role' => new \Twig_Function_Method($this, 'hasRole'),
        );
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->utils->getServices();
    }

    public function getAclEntries($role, $class = NULL)
    {
        return $this->utils->getAclEntries($role, $class);
    }

    public function isGranted($attributes, $object=null, $fully_authenticated = false)
    {
        return $this->utils->isGranted($attributes, $object, $fully_authenticated);
    }

    public function isOwner($object)
    {
        return $this->utils->isOwner($object);
    }

    public function hasRole($role)
    {
        return $this->utils->hasRole($role);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'camdram_security';
    }
}
