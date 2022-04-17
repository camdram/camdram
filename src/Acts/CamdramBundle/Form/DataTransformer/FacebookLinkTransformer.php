<?php

namespace Acts\CamdramBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class FacebookLinkTransformer
 *
 * Transforms a facebook_id from its database representation (page ID or URL) to
 * its user-facing representation (an URL) whenever a form is loaded.
 */
class FacebookLinkTransformer implements DataTransformerInterface
{
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
        } else if (is_numeric($value)) {
            return "https://www.facebook.com/".$value;
        } else {
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
        $value = trim($value);
        if (strpos($value, 'https://') !== 0) {
            $value = "https://$value";
        }
        if (preg_match("/^https:\/\/(www\.)?facebook\.com\/./", $value) === 1) {
            return $value;
        } else {
            throw new TransformationFailedException("This should be an URL starting “https://www.facebook.com/”; other URLs can go in the desciption.");
        }
    }
}
