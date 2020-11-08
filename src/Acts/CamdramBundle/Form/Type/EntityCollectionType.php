<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManagerInterface;
use Acts\CamdramBundle\Form\DataTransformer\EntityCollectionTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * Class EntityCollectionType
 *
 * A form type that presents an autocomplete box which the user can use to add to/remove from a list of linked entities
 */
class EntityCollectionType extends AbstractType
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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

    public function setDefaultOptions(OptionsResolver $resolver)
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
        return CollectionType::class;
    }

    public function getName()
    {
        return 'entity_collection';
    }
}
