<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Acts\CamdramBundle\Entity\EmailBuilder;
use Acts\CamdramBundle\Entity\Show;

/**
 * Class EmailBuilderType
 *
 * The form that's presented when a user adds/edits a email builder
 *
 * @package Acts\CamdramBundle\Form\Type
 */
class EmailBuilderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $showsForFilter = $options['showRepository']->GetShowsWithAnyUpcomingAdvert();
    
        $builder
            ->add('Name')
            ->add('ToAddress')
            ->add('FromAddress')
            ->add('Subject')
            ->add('Title')
            ->add('Introduction', 'textarea')
            ->add('UnsubscribeAddress')
            ->add('IncludeTechieAdverts', 'checkbox', array('required' => false))
            ->add('IncludeAuditions', 'checkbox', array('required' => false))
            ->add('IncludeShowApplications', 'checkbox', array('required' => false))
            ->add('IncludeSocietyApplications', 'checkbox', array('required' => false))
            ->add('ShowFilterMode', 'choice', array(
                'choices' => array(
                    EmailBuilder::FILTERMODEALL => "All Shows",
                    EmailBuilder::FILTERMODEINCLUDE => "Only these shows",
                    EmailBuilder::FILTERMODEEXCLUDE => "Exclude these shows" )
                    ))
            ->add('ShowFilter', 'entity', array(
                'expanded' => true, 
                'multiple' => true,
                'class' => "ActsCamdramBundle:Show",
                'property' => 'nameAndPerformanceRange',
                'choices' => $showsForFilter
                ))
            ->add('SaveAndSend', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acts\CamdramBundle\Entity\EmailBuilder',
            'showRepository' => null
        ));
    }

    public function getName()
    {
        return 'acts_camdrambundle_emailbuildertype';
    }
}
