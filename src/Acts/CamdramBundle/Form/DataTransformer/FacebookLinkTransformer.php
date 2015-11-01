<?php

namespace Acts\CamdramBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Acts\SocialApiBundle\Service\OAuthApi;

/**
 * Class FacebookLinkTransformer
 *
 * Transforms a facebook_id from it's database representation (it's page ID) to it's user-facing representation
 * (it's page username) whenever a form is loaded.
 */
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

    /**
     * Converts a Facebook page ID into the page username
     *
     * @param mixed $value
     *
     * @return mixed|null
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($value)
    {
        if (empty($value)) {
            return null;
        }
        try {
            if (!$this->api->isAuthenticated()) {
                $this->api->authenticateAsSelf();
            }

            $data = $this->api->doGetById($value);
            if (isset($data['error'])) {
                throw new TransformationFailedException(sprintf('%s is an invalid Facebook id', $value));
            } else {
                return $data['username'];
            }
        } catch (\Acts\SocialApiBundle\Exception\SocialApiException $e) {
            //Just return the id, which is valid but less user-friendly
            return $value;
        }
    }

    /**
     * Convert a Facebook page username, URL or ID into its page ID
     *
     * @param mixed $value
     *
     * @return mixed|null
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        if (preg_match('/^(?:https?\:\\/\\/)?www\.facebook\.com\\/([^\?]+)(?:\?.*)?$/i', $value, $matches)) {
            $value = $matches[1];
        }

        try {
            if (!$this->api->isAuthenticated()) {
                $this->api->authenticateAsSelf();
            }

            $data = $this->api->doGetByUsername($value);
            if (isset($data['error'])) {
                throw new TransformationFailedException(sprintf('%s is an invalid Facebook id', $value));
            } else {
                return $data['id'];
            }
        } catch (\Acts\SocialApiBundle\Exception\SocialApiException $e) {
            throw new TransformationFailedException("We cannot accept Facebook pages at this time - we can't communicate with Facebook");
        }
    }
}
