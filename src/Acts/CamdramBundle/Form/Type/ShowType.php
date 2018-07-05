<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * Class ShowType
 *
 * The form that's presented when a user adds/edits a show
 */
class ShowType extends AbstractType
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('author', null, array('required' => false))
            ->add('description')
            ->add('prices', null, array('required' => false, 'label' => 'Ticket prices', 'attr' => array(
                'placeholder' => 'e.g. £6/5'
            )))
            ->add('multi_venue', 'choice', array(
                'expanded' => true,
                'by_reference' => false,
                'choices' => array(
                    'single' => 'All the performances are at the same venue (e.g. an ADC mainshow/lateshow)',
                    'multi' => 'The performances are at a number of different venues (e.g. a tour)',
                ),
            ))
            ->add('performances', CollectionType::class, array(
                'entry_type' => PerformanceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Dates and times'
            ))
            ->add('category', ShowCategoryType::class)
            ->add('venue', EntitySearchType::class, array(
                'route' => 'get_venues',
                'class' => 'Acts\\CamdramBundle\\Entity\\Venue',
                'required' => false,
                'text_field' => 'other_venue'
            ))
            ->add('online_booking_url', UrlType::class, array(
                'required' => false, 'label' => 'URL for purchasing tickets'
            ))
            ->add('facebook_id', FacebookLinkType::class, array('required' => false))
            ->add('twitter_id', TwitterLinkType::class, array('required' => false))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                //society's 'read-only' field is dependent on whether a new show is being created
                $show = $event->getData();
                $form = $event->getForm();

                $disabled = $show
                    && $show->getId() !== null
                    && $show->getSociety() !== null
                    && !$this->authorizationChecker->isGranted('ROLE_ADMIN')
                    ;

                $form->add('society', EntitySearchType::class, array(
                    'route' => 'get_societies',
                    'class' => 'Acts\\CamdramBundle\\Entity\\Society',
                    'required' => false,
                    'disabled' => $disabled,
                    'text_field' => 'other_society'
                ));
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Show'
        ));
    }
}
