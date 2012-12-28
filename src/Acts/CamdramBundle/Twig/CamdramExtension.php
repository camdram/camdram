<?php
namespace Acts\CamdramBundle\Twig;

use Acts\CamdramBundle\Service\TextService;

class CamdramExtension extends \Twig_Extension
{
    /**
     * @var \Acts\CamdramBundle\Service\TextService
     */
    private $textService;

    public function __construct(TextService $textService)
    {
        $this->textService = $textService;
    }

    public function getFilters()
    {
        return array(
            'camdram_markdown' => new \Twig_Filter_Method($this, 'camdramMarkdown', array('is_safe' => array('html'))),
            'detect_links' => new \Twig_Filter_Method($this, 'detectLinks'),
            'strip_new_lines' => new \Twig_Filter_Method($this, 'stripNewLines'),
        );
    }

    public function getFunctions()
    {
        return array(
            'entity_toolbar' => new \Twig_Function_Method($this, 'entityToolbar', array('is_safe' => array('html'), 'needs_environment' => true))
        );
    }

    public function camdramMarkdown($text)
    {
        return $this->textService->convertMarkdown($text);
    }

    public function detectLinks($text)
    {
        return $this->textService->detectLinks($text);
    }

    public function stripNewLines($text)
    {
        return $this->textService->stripNewLines($text);
    }

    public function getName()
    {
        return 'camdram_extension';
    }

    public function entityToolbar(\Twig_Environment $twig, $entity_type, $entity = null, $label = null)
    {
        if ($entity) {
            $routes = array(
                'edit' => 'edit_'.$entity_type,
                'new' => 'new_'.$entity_type,
                'delete' => 'remove_'.$entity_type,
            );
        }
        else {
            $routes = array('new' => 'new_'.$entity_type);
        }
        if (is_null($label)) $label = $entity_type;
        return $twig->render('ActsCamdramBundle:Entity:toolbar.html.twig', array(
            'routes' => $routes,
            'entity' => $entity,
            'label' => $label,
        ));
    }
}