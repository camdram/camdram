<?php
namespace Acts\CamdramSecurityBundle\Security\Service;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Routing\RouterInterface;

class LocalService extends AbstractService
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function getName()
    {
        return 'local';
    }

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getAccessToken(Request $request, $redirectUri, array $extraParameters = array())
    {
        if ($request->getSession()->has('new_local_user_id')) {
            $user_id = $request->getSession()->get('new_local_user_id');
            $request->getSession()->remove('new_local_user_id');
            return $user_id;
        }
    }

    public function getAuthorizationUrl($redirect_uri, array $extraParameters = array())
    {
        return $this->router->generate('camdram_security_local_login');
    }

    public function handles(Request $request)
    {
        return $request->getSession()->has('new_local_user_id');
    }

    public function getUserInfo($token = null)
    {
        return array('id' => $token, 'username' => null, 'name' => null);
    }

}