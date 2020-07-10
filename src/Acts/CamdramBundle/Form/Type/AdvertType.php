<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Acts\CamdramBundle\Entity\Advert;

/**
 * Class AdvertType
 *
 * The form that's presented when a user adds/edits a show
 */
class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Title',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Advert type',
                'expanded' => true,
                'choices'  => [
                    'Auditions' => Advert::TYPE_ACTORS,
                    'Technical Roles' => Advert::TYPE_TECHNICAL,
                    'Designers' => Advert::TYPE_DESIGN,
                    'Director/Producer' => Advert::TYPE_APPLICATION,
                    'Other' => Advert::TYPE_OTHER,
                ],
            ])
            ->add('summary')
            ->add('body', TextareaType::class, [
                'label' => 'Full details',
                'attr' => ['class' => 'large'],
            ])
            ->add('auditions', CollectionType::class, array(
                'entry_type' => AuditionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Sessions'
            ))
            ->add('contactDetails')
            ->add('expiresAt', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'model_timezone' => 'UTC',
                'view_timezone' => 'Europe/London',
            ])
            ->add('display', CheckboxType::class, [
                'label' => 'Make advert visible to the public',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Advert::class
        ));
    }
}
