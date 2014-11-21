<?php


namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FutureDateType extends AbstractType
{
    public function getParent()
    {
        return 'date';
    }

    public function getName()
    {
        return 'futuredate';
    }
}


