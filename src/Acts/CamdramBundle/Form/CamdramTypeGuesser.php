<?php

namespace Acts\CamdramBundle\Form;

use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * Class CamdramTypeGuesser
 *
 * A custom form type guesser, which removes the need to specify the field type if the field name. E.g. if the field
 * is called 'facebook_id', we probably intend it to be a FacebookLinkType
 */
class CamdramTypeGuesser implements FormTypeGuesserInterface
{
    /**
     * {@inheritDoc}
     */
    public function guessType($class, $property)
    {
        switch ($property) {
            case 'college':
                return new TypeGuess('college', array(), Guess::HIGH_CONFIDENCE);
                break;
            case 'facebook_id':
                return new TypeGuess('facebook_link', array(), Guess::HIGH_CONFIDENCE);
                break;
            case 'twitter_id':
                return new TypeGuess('twitter_link', array(), Guess::HIGH_CONFIDENCE);
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function guessRequired($class, $property)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function guessMaxLength($class, $property)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function guessMinLength($class, $property)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function guessPattern($class, $property)
    {
    }
}
