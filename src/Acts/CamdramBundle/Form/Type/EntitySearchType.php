<?php

namespace Acts\CamdramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
        if (!$options['text_field']) $options['text_field'] = $builder->getName().'_name';
        $builder->setAttribute('text_field', $options['text_field']);

        $hidden_name = $builder->getName();
        $text_name = $options['text_field'];

        $repo = $this->em->getRepository($options['class']);

        $builder->add($text_name, 'text', array(
                'attr' => array('class' => 'autocomplete_input')
            ))
            ->add($hidden_name, 'hidden', array(
                'data_class' => $options['class']
            ))
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($repo, $hidden_name, $text_name) {
                $data = $event->getData();
                $obj = null;

                if (is_numeric($data[$hidden_name])) {
                    $obj = $repo->findOneBy(array(
                        'id' => $data[$hidden_name],
                        'name' => $data[$text_name]
                    ));
                }
                if (!$obj && !empty($data[$text_name])) {
                    $obj = $repo->findOneByName($data[$text_name]);
                }

                if (is_null($obj)) {
                    $event->setData(array(
                        $hidden_name => null,
                        $text_name => $data[$text_name]
                    ));
                }
                else {
                    $event->setData(array(
                        $hidden_name => $obj,
                        $text_name => $obj->getName()
                    ));
                }
            })
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['route'] = $options['route'];
        $view->vars['text_id'] = $form->getConfig()->getAttribute('text_field');
        $view->vars['hidden_id'] = $form->getName();

    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $text = $view->children[$form->getConfig()->getAttribute('text_field')];
        $hidden = $view->children[$form->getName()];

        if (is_object($hidden->vars['value'])) {
            $text->vars['value'] = $hidden->vars['value']->getName();
            $hidden->vars['value'] = $hidden->vars['value']->getId();
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'text_field' => null,
            'route' => 'get_people',
            'class' => null,
            'inherit_data' => true,
        ));
    }

    public function getName()
    {
        return 'entity_search';
    }
}
