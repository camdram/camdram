<?php

namespace Acts\CamdramSecurityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreatePasswordType extends AbstractType
{
    /**
     * @var bool
     *
     * Raven accounts don't give us a name, so need to decide whether to include it in the form or not
     */
    private $name_required;

    public function __construct($name_required)
    {
        $this->name_required = $name_required;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', 'repeated', array(
               'first_name'  => 'password',
               'second_name' => 'confirm',
               'second_options' => array('label' => 'Confirm password'),
               'type'        => 'password',
            ))
            ->add('occupation', 'occupation')
            ->add('graduation', 'graduation_year', array('required' => false))
        ;
        if ($this->name_required) {
            $builder->add('name');
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramSecurityBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_usertype';
    }
}
