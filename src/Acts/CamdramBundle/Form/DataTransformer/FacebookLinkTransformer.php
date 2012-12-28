<?php
namespace Acts\CamdramBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

use Acts\SocialApiBundle\Service\OAuthApi;

class FacebookLinkTransformer implements DataTransformerInterface
{
    /**
     * @var \Acts\SocialApiBundle\Service\OAuthApi;
     */
    private $api;

    public function __construct(OAuthApi $api)
    {
        $this->api = $api;
    }

    public function transform($value) {
        if (empty($value)) return NULL;
        try {
            if (!$this->api->isAuthenticated()) $this->api->authenticateAsSelf();

            $data = $this->api->doGetById($value);
            if (isset($data['error'])) {
                throw new TransformationFailedException(sprintf('%s is an invalid Facebook id', $value));
            }
            else {
                return $data['username'];
            }
        }
        catch (\Acts\SocialApiBundle\Exception\SocialApiException $e) {
            //Just return the id, which is valid but less user-friendly
            return $value;
        }
    }

    public function reverseTransform($value) {
        if (empty($value)) return NULL;

        if (preg_match('/^(?:https?\:\\/\\/)?www\.facebook\.com\\/([^\?]+)(?:\?.*)?$/i', $value, $matches)) {
            $value = $matches[1];
        }

        try {
            if (!$this->api->isAuthenticated()) $this->api->authenticateAsSelf();

            $data = $this->api->doGetByUsername($value);
            if (isset($data['error'])) {
                throw new TransformationFailedException(sprintf('%s is an invalid Facebook id', $value));
            }
            else {
                return $data['id'];
            }
        }
        catch (\Acts\SocialApiBundle\Exception\SocialApiException $e) {
            throw new TransformationFailedException("We cannot accept Facebook pages at this time - we can't communicate with Facebook");
        }
    }

}