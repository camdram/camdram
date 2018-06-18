<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use Acts\CamdramBundle\Form\DataTransformer\EntityCollectionTransformer;

/**
 * Class EntityCollectionType
 *
 * A form type that presents an autocomplete box which the user can use to add to/remove from a list of linked entities
 */
class EntityCollectionType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('new_entity', 'text', array(
            'label' => 'Add',
            'attr' => array('class' => 'autocomplete_input'),
            'required' => false,
        ));
        $builder->addViewTransformer(new EntityCollectionTransformer($this->em, $options['class']));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['route'] = $options['route'];
        $view->vars['new_label'] = $options['new_label'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'route' => null,
            'class' => null,
            'allow_add' => true,
            'allow_delete' => true,
            'new_label' => 'Add'
        ));
    }

    public function getParent()
    {
        return 'collection';
    }

    public function getName()
    {
        return 'entity_collection';
    }
}
