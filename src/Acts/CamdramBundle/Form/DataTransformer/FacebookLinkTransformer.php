<?php

namespace Acts\CamdramBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Acts\SocialApiBundle\Service\OAuthApi;
use Facebook\Facebook;

/**
 * Class FacebookLinkTransformer
 *
 * Transforms a facebook_id from it's database representation (it's page ID) to it's user-facing representation
 * (it's page username) whenever a form is loaded.
 */
class FacebookLinkTransformer implements DataTransformerInterface
{
    /**
     * @var Facebook
     */
    private $api;

    public function __construct(Facebook $api)
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

            $data = $this->api->sendRequest('GET', '/'.$value, ['fields' => 'username']);
            return $data->getDecodedBody()['username'];
        }
        catch(\Facebook\Exceptions\FacebookResponseException $e) {
            throw new TransformationFailedException(sprintf('%s is an invalid Facebook id', $value));
        }
        catch(\Facebook\Exceptions\FacebookSDKException $e) {
            //Just return the id, which is valid but less user-friendly
            die('xx');
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
            $data = $this->api->get('/'.urlencode($value));
            return $data->getDecodedBody()['id'];
        } 
        catch(\Facebook\Exceptions\FacebookResponseException $e) {
            throw new TransformationFailedException(sprintf('%s is an invalid Facebook id', $value));
        }
        catch(\Facebook\Exceptions\FacebookSDKException $e) {
            throw new TransformationFailedException("We cannot accept Facebook pages at this time - we can't communicate with Facebook");
        }
    }
}
