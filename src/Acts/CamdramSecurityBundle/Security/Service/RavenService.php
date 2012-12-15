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
        $params['desc'] = urlencode($this->getOption('description'));

        $parameters = array();
        foreach ($params as $key => $val) {
            $parameters[] = $key . '=' . utf8_encode($val);
        }
        $parameters = '?' . implode('&', $parameters);

        return $this->getUrl() .$parameters;
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

        $key = openssl_pkey_get_public($this->getCertificate());

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

    public function getUrl()
    {
        return 'https://raven.cam.ac.uk/auth/authenticate.html';
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificate()
    {
        return '-----BEGIN CERTIFICATE-----
MIIDrTCCAxagAwIBAgIBADANBgkqhkiG9w0BAQQFADCBnDELMAkGA1UEBhMCR0Ix
EDAOBgNVBAgTB0VuZ2xhbmQxEjAQBgNVBAcTCUNhbWJyaWRnZTEgMB4GA1UEChMX
VW5pdmVyc2l0eSBvZiBDYW1icmlkZ2UxKDAmBgNVBAsTH0NvbXB1dGluZyBTZXJ2
aWNlIFJhdmVuIFNlcnZpY2UxGzAZBgNVBAMTElJhdmVuIHB1YmxpYyBrZXkgMjAe
Fw0wNDA4MTAxMzM1MjNaFw0wNDA5MDkxMzM1MjNaMIGcMQswCQYDVQQGEwJHQjEQ
MA4GA1UECBMHRW5nbGFuZDESMBAGA1UEBxMJQ2FtYnJpZGdlMSAwHgYDVQQKExdV
bml2ZXJzaXR5IG9mIENhbWJyaWRnZTEoMCYGA1UECxMfQ29tcHV0aW5nIFNlcnZp
Y2UgUmF2ZW4gU2VydmljZTEbMBkGA1UEAxMSUmF2ZW4gcHVibGljIGtleSAyMIGf
MA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC/9qcAW1XCSk0RfAfiulvTouMZKD4j
m99rXtMIcO2bn+3ExQpObbwWugiO8DNEffS7bzSxZqGp7U6bPdi4xfX76wgWGQ6q
Wi55OXJV0oSiqrd3aOEspKmJKuupKXONo2efAt6JkdHVH0O6O8k5LVap6w4y1W/T
/ry4QH7khRxWtQIDAQABo4H8MIH5MB0GA1UdDgQWBBRfhSRqVtJoL0IfzrSh8dv/
CNl16TCByQYDVR0jBIHBMIG+gBRfhSRqVtJoL0IfzrSh8dv/CNl16aGBoqSBnzCB
nDELMAkGA1UEBhMCR0IxEDAOBgNVBAgTB0VuZ2xhbmQxEjAQBgNVBAcTCUNhbWJy
aWRnZTEgMB4GA1UEChMXVW5pdmVyc2l0eSBvZiBDYW1icmlkZ2UxKDAmBgNVBAsT
H0NvbXB1dGluZyBTZXJ2aWNlIFJhdmVuIFNlcnZpY2UxGzAZBgNVBAMTElJhdmVu
IHB1YmxpYyBrZXkgMoIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBAUAA4GB
AFciErbr6zl5i7ClrpXKA2O2lDzvHTFM8A3rumiOeauckbngNqIBiCRemYapZzGc
W7fgOEEsI4FoLOjQbJgIrgdYR2NIJh6pKKEf+9Ts2q/fuWv2xOLw7w29PIICeFIF
hAM+a6/30F5fdkWpE1smPyrfASyXRfWE4Ccn1RVgYX9u
-----END CERTIFICATE-----';
    }

}