<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Acts\CamdramBundle\Form\DataTransformer\PerformanceExcludeTransformer;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class ShowType
 *
 * The form that's presented when a user adds/edits a show
 *
 * @package Acts\CamdramBundle\Form\Type
 */
class ShowType extends AbstractType
{
    private $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
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
                'data_class' => 'Acts\\CamdramBundle\\Entity\\Show',
                'other_mapped' => true,
                'required' => false,
            ))
            ->add('booking_code', null, array(
                'required' => false, 'label' => 'Web page to buy tickets'
            ))
            ->add('facebook_id', null, array('required' => false))
            ->add('twitter_id', null, array('required' => false))
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                //society's 'read-only' field is dependent on whether a new show is being created
                $show = $event->getData();
                $form = $event->getForm();

                $form->add('society','entity_search', array(
                    'route' => 'get_societies',
                    'class' => 'Acts\\CamdramBundle\\Entity\\Society',
                    'data_class' => 'Acts\\CamdramBundle\\Entity\\Show',
                    'other_mapped' => true,
                    'required' => false,
                    'disabled' => $show && null !== $show->getId() && !$this->securityContext->isGranted('ROLE_ADMIN')
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
        return 'acts_camdrambundle_showtype';
    }
}
