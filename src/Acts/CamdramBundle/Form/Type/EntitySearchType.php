<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;

use Acts\CamdramBundle\Form\DataTransformer\EntitySearchTransformer;

/**
 * Class EntitySearchType
 *
 * A form type that presents an autocomplete box which the user can use to search for a linked entity
 *
 * @package Acts\CamdramBundle\Form\Type
 */
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
        if (!$options['other_field']) $options['other_field'] = $builder->getName().'_name';
        $builder->setAttribute('other_field', $options['other_field']);

        $builder->add($options['other_field'], 'text', array('attr' => array('class' => 'autocomplete_input'), 'mapped' => $options['other_mapped']))
            ->add($builder->create($builder->getName(), 'hidden')->addModelTransformer(new EntitySearchTransformer($this->em, $options['class'], 'id')))
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['route'] = $options['route'];
        $view->vars['text_id'] = $form->getConfig()->getAttribute('other_field');
        $view->vars['hidden_id'] = $form->getName();
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $text = $view->children[$form->getConfig()->getAttribute('other_field')];
        $hidden = $view->children[$form->getName()];

        $data = $hidden->vars['value'];

        if (empty($text->vars['value'])) $text->vars['value'] = $data['name'];
        $hidden->vars['value'] = $data['id'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => null,
            'route' => 'get_people',
            'compound' => true,
            'virtual' => true,
            'other_field' => null,
            'other_mapped' => false,
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'entity_search';
    }
}
