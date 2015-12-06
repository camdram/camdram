<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
            ->add('image', 'image_upload', array('label' => 'Publicity image', 'required' => false))
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
            ->add('performances', 'collection', array(
                'type' => new PerformanceType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Dates and times'
            ))
            ->add('category', 'show_category')
            ->add('venue', 'entity_search', array(
                'route' => 'get_venues',
                'class' => 'Acts\\CamdramBundle\\Entity\\Venue',
                'required' => false,
                'text_field' => 'other_venue'
            ))
            ->add('online_booking_url', 'url', array(
                'required' => false, 'label' => 'URL for purchasing tickets'
            ))
            ->add('facebook_id', null, array('required' => false))
            ->add('twitter_id', null, array('required' => false))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                //society's 'read-only' field is dependent on whether a new show is being created
                $show = $event->getData();
                $form = $event->getForm();

                $disabled = $show
                    && $show->getId() !== null
                    && $show->getSociety() !== null
                    && !$this->authorizationChecker->isGranted('ROLE_ADMIN')
                    ;

                $form->add('society', 'entity_search', array(
                    'route' => 'get_societies',
                    'class' => 'Acts\\CamdramBundle\\Entity\\Society',
                    'required' => false,
                    'disabled' => $disabled,
                    'text_field' => 'other_society'
                ));

            })
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\Show'
        ));
    }

    public function getName()
    {
        return 'show';
    }
}
