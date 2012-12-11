<?php
namespace Acts\CamdramSecurityBundle\Security\Service;
use Symfony\Component\HttpFoundation\Request,
        Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RavenService extends AbstractService
{
    public function getName()
    {
        return 'raven';
    }

    public function getAccessToken(Request $request, $redirectUri, array $extraParameters = array())
    {
        if ($request->query->has('WLS-Response')) {
            $wlsResponse = $request->query->get('WLS-Response');
            $request->query->remove('WLS-Response');

            list($ver, $status, $msg, $issue, $id, $url, $principal, $auth, $sso, $life, $params, $kid, $sig) = explode(
                '!',
                $wlsResponse
            );

            $token = array(
                'principal' => $principal,
                'ver' => (int) $ver,
                'status' => (int) $status,
                'msg' => $msg != '' ? (string) $msg : null,
                'issue' => new \DateTime($issue),
                'id' => (string) $id,
                'url' => (string) $url,
                'auth' => $auth != '' ? (string) $auth : null,
                'sso' => (string) $sso,
                'life' => $life != '' ? (int) $life : null,
                'params' => $params != '' ? (string) $params : null,
                'kid' => (int) $kid,
                'sig' => (string) $sig,
            );
            if ((time() - $token['issue']->getTimestamp() > 30)) {
                //TODO: throw new LoginTimedOutException();
            }
            if ($this->validateToken($token)) {
                return $token;
            }
        }
    }

    public function getAuthorizationUrl($redirect_uri, array $extraParameters = array())
    {
        $params['ver'] = 2;
        $params['url'] = urlencode($redirect_uri);
        //$params['desc'] = urlencode($this->description);

        $parameters = array();
        foreach ($params as $key => $val) {
            $parameters[] = $key . '=' . utf8_encode($val);
        }
        $parameters = '?' . implode('&', $parameters);

        return $this->api->getUrl() .$parameters;
    }

    public function handles(Request $request)
    {
        return $request->query->has('WLS-Response');
    }

    public function getUserInfo($token = null)
    {
        return array('id' => null, 'username' => $token['principal'], 'name' => null);
    }

    /**
     * Validate RavenUserToken.
     *
     * @param RavenUserToken $token Raven user token.
     *
     * @return bool true if the token is valid, false otherwise.
     *
     * @throws Exception
     */
    protected function validateToken($token)
    {
        $data = rawurldecode(
            implode(
                '!',
                array(
                    $token['ver'],
                    $token['status'],
                    $token['msg'],
                    $token['issue']->format('Ymd\THis\Z'),
                    $token['id'],
                    $token['url'],
                    $token['principal'],
                    $token['auth'],
                    $token['sso'],
                    $token['life'],
                    $token['params'],
                )
            )
        );

        $sig = base64_decode(
            preg_replace(
                array(
                    '/-/',
                    '/\./',
                    '/_/',
                ),
                array(
                    '+',
                    '/',
                    '=',
                ),
                rawurldecode($token['sig'])
            )
        );

        $key = openssl_pkey_get_public($this->api->getCertificate());

        $result = openssl_verify($data, $sig, $key);

        openssl_free_key($key);

        switch ($result) {
            case 1:
                return true;
                break;
            case 0:
                return false;
                break;
            default:
                throw new Exception('OpenSSL error');
                break;
        }
    }

}