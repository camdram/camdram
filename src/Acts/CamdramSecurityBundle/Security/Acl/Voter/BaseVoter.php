<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 04/04/15
 * Time: 18:28
 */

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;


use Acts\ExternalLoginBundle\Security\Authentication\Token\ExternalLoginToken;
use FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\Role;

abstract class BaseVoter implements VoterInterface
{

    /**
     * Is an API request
     *
     * @param TokenInterface $token
     * @return bool
     */
    protected function isApiRequest(TokenInterface $token)
    {
        return $token instanceof OAuthToken;
    }

    /**
     * Is an interactive user request (i.e. not an API request)
     *
     * @param TokenInterface $token
     * @return bool
     */
    protected function isInteractiveRequest(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordToken || $token instanceof ExternalLoginToken;
    }

    /**
     * Utility function to find role in token
     *
     * @param TokenInterface $token
     * @param $role
     * @return bool
     */
    protected function hasRole(TokenInterface $token, $role)
    {
        if (is_string($role)) $role = new Role($role);


        foreach ($token->getRoles() as $tokenRole) {
            if ($role->getRole() == $tokenRole->getRole()) return true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, $this->getSupportedAttributes());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        foreach ($this->getSupportedClasses() as $supportedClass) {
            if ($supportedClass === $class || is_subclass_of($class, $supportedClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Iteratively check all given attributes by calling isGranted
     *
     * This method terminates as soon as it is able to return ACCESS_GRANTED
     * If at least one attribute is supported, but access not granted, then ACCESS_DENIED is returned
     * Otherwise it will return ACCESS_ABSTAIN
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param object         $object     The object to secure
     * @param array          $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$object || !$this->supportsClass(get_class($object))) {
            return self::ACCESS_ABSTAIN;
        }

        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            // as soon as at least one attribute is supported, default is to deny access
            $vote = self::ACCESS_DENIED;

            if ($this->isGranted($attribute, $object, $token)) {
                // grant access as soon as at least one voter returns a positive response
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    /**
     * Return an array of supported classes. This will be called by supportsClass
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    abstract protected function getSupportedClasses();

    /**
     * Return an array of supported attributes. This will be called by supportsAttribute
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    abstract protected function getSupportedAttributes();

    /**
     * Perform a single access check operation on a given attribute, object and (optionally) user
     * It is safe to assume that $attribute and $object's class pass supportsAttribute/supportsClass
     * $user can be one of the following:
     *   a UserInterface object (fully authenticated user)
     *   a string               (anonymously authenticated user)
     *
     * @param string               $attribute
     * @param object               $object
     * @param TokenInterface|null  $token
     *
     * @return bool
     */
    abstract protected function isGranted($attribute, $object, TokenInterface $token);


}