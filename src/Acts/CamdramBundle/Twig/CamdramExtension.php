<?php
namespace Acts\CamdramBundle\Twig;

use Acts\CamdramBundle\Service\TextService;

/**
 * Class CamdramExtension
 *
 * A Twig extension which provides custom functionality that can be used in Twig templates. The extension is registered
 * in services.yml
 *
 * @package Acts\CamdramBundle\Twig
 */
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

    /**
     * Defines the custom Twig filters
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'camdram_markdown' => new \Twig_Filter_Method($this, 'camdramMarkdown', array('is_safe' => array('html'))),
            'detect_links' => new \Twig_Filter_Method($this, 'detectLinks', array('is_safe' => array('html'))),
            'strip_new_lines' => new \Twig_Filter_Method($this, 'stripNewLines'),
            'truncate' => new \Twig_Filter_Method($this, 'truncate', array('pre_escape' => 'html', 'is_safe' => array('html'))),
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

    public function truncate($text, $length)
    {
        return $this->textService->truncate($text, $length);
    }

    public function getName()
    {
        return 'camdram_extension';
    }

}