<?php
namespace Acts\CamdramSecurityBundle\Security;

use HWI\Bundle\OAuthBundle\Security\Http\ResourceOwnerMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Http\Client\HttpClient;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse;
use OAuth2\Model\OAuth2Token;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use HWI\Bundle\OAuthBundle\OAuth\StateInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class RavenResourceOwner implements ResourceOwnerInterface
{
    private $name;

    /** @var StateInterface */
    private $state;

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOption($name)
    {
        return false;
    }

    public function getAccessToken(Request $request, $redirectUri, array $extraParameters = array()): array
    {
        if (!$request->query->has('WLS-Response')) {
            throw new AuthenticationException('Raven: No WLS_Response in request');
        }

        $wlsResponse = $request->query->get('WLS-Response');
        $request->query->remove('WLS-Response');

        $parts = explode('!', $wlsResponse);
        $ver = array_shift($parts);
        if ($ver == 3) {
            if (count($parts) != 13) {
                throw new AuthenticationException('Raven: Invalid number of parts in WLS-Response');
            }
            list($status, $msg, $issue, $id, $url, $principal, $ptags, $auth, $sso, $life, $params, $kid, $sig) = $parts;
        }
        else {
            if (count($parts) != 12) {
                throw new AuthenticationException('Raven: Invalid number of parts in WLS-Response');
            }
            list($status, $msg, $issue, $id, $url, $principal, $auth, $sso, $life, $params, $kid, $sig) = $parts;
        }

        if ($status == 410) {
            throw new CustomUserMessageAuthenticationException('Raven login cancelled.');
        }
        else if ($status != 200) {
            switch ($status) {
                case 510:
                    throw new AuthenticationException('Raven error: No mutually acceptable authentication types available');
                case 520:
                    throw new AuthenticationException('Raven error: Unsupported protocol version');
                case 530:
                    throw new AuthenticationException('Raven error: General request parameter error');
                case 540:
                    throw new AuthenticationException('Raven error: Interaction would be required');
                case 560:
                    throw new AuthenticationException('Raven error: WAA not authorised');
                case 570:
                    throw new AuthenticationException('Raven error: Authentication declined');
                default:
                    throw new AuthenticationException('Raven error: Unknown '.$status);
            }
        }

        $token = array(
            'principal' => $principal,
            'ver' => (int) $ver,
            'status' => (int) $status,
            'msg' => $msg != '' ? (string) $msg : null,
            'issue' => new \DateTime($issue),
            'id' => (string) $id,
            'ptags' => (string) $ptags,
            'url' => (string) $url,
            'auth' => $auth != '' ? (string) $auth : null,
            'sso' => (string) $sso,
            'life' => $life != '' ? (int) $life : null,
            'params' => $params != '' ? (string) $params : null,
            'kid' => (int) $kid,
            'sig' => (string) $sig,
            'access_token' => ''
        );

        if (abs(time() - $token['issue']->getTimestamp()) > 30) {
            throw new AuthenticationException('Raven: log in timed out');
        }
        if (!$this->validateToken($token)) {
            throw new AuthenticationException('Raven: token validation failed');
        }

        return $token;
    }

    public function addPaths(array $paths)
    {
    }

    public function refreshAccessToken($refreshToken, array $extraParameters = [])
    {
    }

    public function getAuthorizationUrl($redirect_uri, array $params = array())
    {
        $params['ver'] = 3;
        $params['url'] = urlencode($redirect_uri);
        $params['desc'] = 'Camdram';

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

    public function isCsrfTokenValid($csrfToken)
    {
        return true;
    }

    public function getUserInformation(array $token, array $extraParameters = array())
    {
        $response = new PathUserResponse;
        $data = [
            'identifier' => $token['principal'],
            'name' => null
        ];
        if ($token['ptags'] == 'current')
        {
            // We can only infer an e-mail address for current students.
            $data['email'] = $token['principal'].'@cam.ac.uk';
        }

        $response->setData($data);
        $response->setResourceOwner($this);
        $response->setOAuthToken(new OAuthToken($token['ptags']));
        $response->setPaths(['identifier' => 'identifier', 'email' => 'email']);

        return $response;
    }

    /**
     * Validate RavenUserToken.
     *
     * @param array<mixed> $token Raven user token.
     * @return bool true if the token is valid, false otherwise.
     * @throws \Exception
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
                    $token['ptags'],
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
            case 0:
                return false;
            default:
                throw new \Exception('OpenSSL error');
        }
    }

    public function getUrl(): string
    {
        return 'https://nevar.srcf.net/wls/authenticate';
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificate(): string
    {
        return '-----BEGIN CERTIFICATE-----
MIIF3zCCA8cCFDxoz6/Bid9w+C/ElimKELXotHINMA0GCSqGSIb3DQEBCwUAMIGr
MQswCQYDVQQGEwJHQjEXMBUGA1UECAwOQ2FtYnJpZGdlc2hpcmUxEjAQBgNVBAcM
CUNhbWJyaWRnZTEnMCUGA1UECgweU3R1ZGVudC1SdW4gQ29tcHV0aW5nIEZhY2ls
aXR5MRcwFQYDVQQDDA5uZXZhci5zcmNmLm5ldDEtMCsGCSqGSIb3DQEJARYec29j
LXNyY2YtYWRtaW5AbGlzdHMuY2FtLmFjLnVrMB4XDTI0MTEyNDE4MjEyMloXDTM0
MTEyNDE4MjEyMlowgasxCzAJBgNVBAYTAkdCMRcwFQYDVQQIDA5DYW1icmlkZ2Vz
aGlyZTESMBAGA1UEBwwJQ2FtYnJpZGdlMScwJQYDVQQKDB5TdHVkZW50LVJ1biBD
b21wdXRpbmcgRmFjaWxpdHkxFzAVBgNVBAMMDm5ldmFyLnNyY2YubmV0MS0wKwYJ
KoZIhvcNAQkBFh5zb2Mtc3JjZi1hZG1pbkBsaXN0cy5jYW0uYWMudWswggIiMA0G
CSqGSIb3DQEBAQUAA4ICDwAwggIKAoICAQDQNqOK4ZYH2ml26JoL/2y4du42krg6
oVPHCBugt6Q6UxoWgeWKF27XHEw6efo0dCt1dIfYLjUSZfIxB6c7sWHYR53iJxuF
y/55PS8I46QT7cG3I9wFRw90d191VaUs219QmZ6PiS4crlf0ueXcuRWGWhNfN5vX
zpQuBw05E/ul6MhQ+xTgh6eM1sjaW3DP+S7CnOPkOKY621XPQ513Vs7FQq3euess
PwAhPxt492PhpAFY/SAW5C990fGik5q9N7737zYMrxrvd9KJ9kcRsgiZjBkm+4Jl
pb1tO2zpKWsdLaXgpYFm8kRixUjTEGX2/sOZhBWRq4dnwSGicRrOuxA/KHRACHaY
aROSC61Rd1f5yorVQw2SS0AMupOEC3no436rzRavR0/SYbaUchltnfRHKrnHnM3z
T1JIrn5JQFwhq4MhHCAs9128dZxxC+mmM1hF9+ki+ytnewtyVXrnkJWV4twsRBQK
Uk+HnYUKjUi5l7N3GCPzRfnpi+JotVnWUOfmnJx78f15Lcr/rP9hwh818hZWC49R
S5UvqsJ48QwnYk9CPb0mKGVgmGW5em0pLFi8VQWXYTzTmZIlehsV8JVWUizy/fQY
hCiRCvBSIvi5JF4Wsrr05jYiO83yHs6S6cQ66RvO5htJcpWkIu5+Jet46Yg3apWm
0AzInXS9iK7eqQIDAQABMA0GCSqGSIb3DQEBCwUAA4ICAQAQ5AUnKgJCSbU6zK0X
JEUUWJvDRtSMh2DZNWP0M4zK69Tj6iv/mZ+8WTJtvG1NmxpF8LQ46/geJsgb4wHX
Xiz77rOv6ommG9XW4Ctmu6G2Q7l8WFpYw5DvzIblSk1t5dlq2FzJeKWiN5USc4iY
3gf5tqMCH8/dLw8GPbZ9gdfrefxZVwkKHICOhZboctDwvT/f4KrwON/V/nxYZOZq
RW3YDoEhdnmo+ywkkHW8rhDUaLVzTofDhmnFJ8Np8vEyWrcsaXvookfLMnEy9075
E+9pwdqsZt7mOaWPI9UmfglKCEnoJT+gIZD4y3fYiFiedlcpVR7I9oaPxIfkiSUT
o2VRccxd4jk2S9BxP7/aB9J6+v9OLVFc5jLnc5+CtkdE83LX32JjwoQitwepUBps
wMwULPrlKuhptG/TH80dw/FTGCGyCK3NVPd4pRXHepyudSOjupgL+b2DYxiwlVco
kEd988KcLhfR/4nPdHsyPtAPQ9jW1TI3XKyQ8glI4fYP5bToRctqkFNPUwjBCg99
eq7f1/HecUdZxceyQhsuEu+Q84v26b23M/c/uIJVffjhzyNGrqH1hydISCkdsfDo
4GPhsPWXJNE+Rz4mL9xexWqiR4DkJ09uF87o9fa/A7z1uqQSfQsq+RQeK7hB3QOh
xJgOQZlHoqrRJqReKnAmfHdMDg==
-----END CERTIFICATE-----';
    }

    public function getState(): StateInterface
    {
        return $this->state;
    }

    public function storeState(StateInterface $state = null)
    {
        $this->state = $state;
    }

    public function addStateParameter(string $key, string $value): void
    {

    }
}
