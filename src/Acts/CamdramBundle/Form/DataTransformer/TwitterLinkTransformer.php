<?php
namespace Acts\CamdramBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

use Acts\SocialApiBundle\Service\OAuthApi;

/**
 * Class TwitterLinkTransformer
 *
 * Transforms a twitter_id from it's database representation (it's account ID) to it's user-facing representation
 * (it's account name) whenever a form is loaded.
 *
 * @package Acts\CamdramBundle\Form\DataTransformer
 */
class TwitterLinkTransformer implements DataTransformerInterface
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
     * Converts a Twitter account ID into a Twitter account name
     *
     * @param mixed $value
     * @return mixed|null
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($value)
    {
        if (empty($value)) return NULL;

        try {
            if (!$this->api->isAuthenticated()) $this->api->authenticateAsSelf();

            $data = $this->api->doGetById($value);
            if (isset($data['error'])) {
                throw new TransformationFailedException(sprintf('%s is an invalid Twitter id', $value));
            } else {

                return $data['username'];
            }
        } catch (\Acts\SocialApiBundle\Exception\SocialApiException $e) {
            //Just return the id, which is valid but less user-friendly
            return $value;
        }
    }

    /**
     * Converts a Twitter account name, URL or ID into a Twitter account ID
     *
     * @param mixed $value
     * @return mixed|null
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value)
    {
        if (empty($value)) return NULL;

        if (preg_match('/^(?:https?\:\\/\\/)?www\.twitter\.com\\/([^\?]+)(?:\?.*)?$/i', $value, $matches)) {
            $value = $matches[1];
        }

        try {
            if (!$this->api->isAuthenticated()) $this->api->authenticateAsSelf();

            $data = $this->api->doGetByUsername($value);
            if (isset($data['error'])) {
                throw new TransformationFailedException(sprintf('%s is an invalid Twitter id', $value));
            } else {
                return $data['id'];
            }
        } catch (\Acts\SocialApiBundle\Exception\SocialApiException $e) {
            throw new TransformationFailedException("We cannot accept Twitter accounts at this time - we can't communicate with Twitter");
        }
    }

}
