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
            'camdram_markdown' => new \Twig_Filter_Method($this, 'camdramMarkdown', array('is_safe' => array('all'))),
            'detect_links' => new \Twig_Filter_Method($this, 'detectLinks'),
            'strip_new_lines' => new \Twig_Filter_Method($this, 'stripNewLines'),
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
}