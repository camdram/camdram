<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints;

/**
 * Class RoleType
 *
 * The form that's presented when when adding multiple roles simultaneously
 * to a show.
 */
class RolesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ordering', ChoiceType::class, array(
                'choices' => array(
                    'Name of role followed by the person (e.g. Sound Designer: James Dooley)' => 'role_first',
                    'Name of person followed by their role (e.g. James Dooley: Sound Designer)' => 'name_first'),
                'expanded' => true,
                'data' => 'role_first',
                'constraints' => [new Constraints\NotBlank()]
                ))
            ->add('separator', TextType::class, array(
                'attr' => array('placeholder' => ':'),
                'constraints' => [new Constraints\NotBlank()]
                ))
            ->add('list', TextareaType::class, array(
                'attr' => array(
                    'placeholder' => 'Sound Designer: James Dooley',
                    'rows' => '10',
                    ),
                'constraints' => [new Constraints\NotBlank()]
                ))
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    'Cast' => 'cast',
                    'Production Team' => 'prod',
                    'Band/Orchestra' => 'band'
                    ),
                'constraints' => [new Constraints\NotBlank()]
                ))
            ->add('add_roles', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => null));
    }
}
