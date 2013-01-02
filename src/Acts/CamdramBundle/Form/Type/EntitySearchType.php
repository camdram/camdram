<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;

use Acts\CamdramBundle\Form\DataTransformer\EntitySearchTransformer;

class EntitySearchType extends AbstractType
{
    /**
    * @var EntityManager
    */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('attr' => array('class' => 'autocomplete_input'), 'mapped' => false))
            ->add('id', 'hidden')
            ->addModelTransformer(new EntitySearchTransformer($this->em, $options['class']))
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['route'] = $options['route'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => null,
            'route' => 'get_people',
            'error_bubbling' => false,
        ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'entity_search';
    }
}
