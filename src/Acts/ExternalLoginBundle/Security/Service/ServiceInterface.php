<?php
namespace Acts\CamdramSecurityBundle\Security\Service;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * ResourceOwnerInterface
 *
 * @author Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
interface ServiceInterface
{
    /**
     * Retrieves the user's information from an access_token
     *
     * @param string $accessToken
     *
     * @return UserResponseInterface The wrapped response interface.
     */
    public function getUserInfo($access_token = null);

    /**
     * Returns the provider's authorization url
     *
     * @param mixed $redirectUri     The uri to redirect the client back to
     * @param array $extraParameters An array of parameters to add to the url
     *
     * @return string The authorization url
     */
    public function getAuthorizationUrl($redirect_uri, array $extra_parameters = array());

    /**
     * Retrieve an access token for a given code
     *
     * @param Request $request         The request object where is going to extract the code from
     * @param mixed   $redirectUri     The uri to redirect the client back to
     * @param array   $extraParameters An array of parameters to add to the url
     *
     * @return string The access token
     */
    public function getAccessToken(Request $request, $redirect_uri, array $extra_parameters = array());

    /**
     * Return a name for the resource owner.
     *
     * @return string
     */
    public function getName();

    /**
     * Checks whether the class can handle the request.
     *
     * @param Request $request
     *
     * @return boolean
     */
    public function handles(Request $request);

    /**
     * Sets a name for the resource owner.
     */
   // public function setName($name);

}
