<?php

namespace Acts\CamdramSecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GraduationYearType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $years = array();
        $cur_year = date('Y');
        for ($i = 8; $i >= -50; $i--) {
            $years[$cur_year + $i] = $cur_year + $i;
        }

        $resolver->setDefaults(array(
            'choices' => $years,
            'empty_value' => 'n/a',
            'label' => 'Year of graduation (if applicable)'
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'graduation_year';
    }
}
