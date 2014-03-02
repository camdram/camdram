<?php
namespace Acts\CamdramBundle\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CamdramFormTypeExtension
 *
 * A custom form extension which modifies the default formatting of forms to match Foundation's styling.
 *
 * @package Acts\CamdramBundle\Form
 */
class CamdramFormTypeExtension extends AbstractTypeExtension
{
    /**
    * Returns the name of the type being extended.
    *
    * @return string The name of the type being extended
    */
    public function getExtendedType()
    {
        return 'form';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label_attr']['class'] = 'right inline';
        //var_dump($view);
        if (!$view->vars['valid']) {
            $view->vars['attr']['class'] = 'error';
            $view->vars['label_attr']['class'] .= ' error';
        }
    }


}
