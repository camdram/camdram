<?php
namespace Acts\CamdramSecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OccupationType extends AbstractType
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                'Yes, Cambridge University' => 'Yes, Cambridge University',
                'Yes, ARU' => 'Yes, ARU',
                'Yes, another university' => 'Yes, another university',
                'No' => 'No',
            ),
            'label' => 'Are you a current University student or member of staff?'
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'occupation';
    }
}
