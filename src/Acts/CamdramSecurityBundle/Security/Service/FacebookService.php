<?php
namespace Acts\CamdramSecurityBundle\Security\Service;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class FacebookService extends AbstractService
{
    public function getName()
    {
        return 'facebook';
    }

    public function getAuthorizationUrl($redirect_uri, array $extraParameters = array())
    {
        return $this->api->getLoginUrl(
            array(
                'display' => 'page',
                'scope' => 'email',
                'redirect_uri' => $redirect_uri,
            ));
    }

    public function getAccessToken(Request $request, $redirectUri, array $extraParameters = array())
    {
        return $this->api->getAccessToken();
    }

    public function handles(Request $request)
    {
        return $request->query->has('code');
    }

    public function getUserInfo($access_token = null)
    {
        $info = $this->api->api('/me');
        return array('id' => $info['id'], 'username' => $info['username'], 'name' => $info['name']);
    }




}
