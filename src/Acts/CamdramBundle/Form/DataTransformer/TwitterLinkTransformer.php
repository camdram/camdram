<?php

namespace Acts\CamdramBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class TwitterLinkTransformer
 *
 * Transforms a twitter_id from it's database representation (it's account ID) to it's user-facing representation
 * (it's account name) whenever a form is loaded.
 */
class TwitterLinkTransformer implements DataTransformerInterface
{
    /**
     * @var TwitterOAuth
     */
    private $api;

    public function __construct(TwitterOAuth $api)
    {
        $this->api = $api;
        $this->api->setDecodeJsonAsArray(true);
    }

    /**
     * Converts a Twitter account ID into a Twitter account name
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

        $data = $this->api->get('users/show', ['user_id' => $value]);
        if ($this->api->getLastHttpCode() == 200) {
            return $data['screen_name'];
        } else {
            return $value;
        }
    }

    /**
     * Converts a Twitter account name, URL or ID into a Twitter account ID
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

        if (preg_match('/^(?:https?\:\\/\\/)?(?:www\.)?twitter\.com\\/([^\?]+)(?:\?.*)?$/i', $value, $matches)) {
            $value = $matches[1];
        }

        if (is_numeric($value)) {
            return $value;
        }

        $data = $this->api->get('users/show', ['screen_name' => $value]);
        if ($this->api->getLastHttpCode() == 200) {
            return $data['id'];
        } else {
            throw new TransformationFailedException(sprintf('%s is an invalid Twitter id', $value));
        }
    }
}
