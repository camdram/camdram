<?php

namespace Acts\CamdramBundle\Twig;

use Acts\CamdramBundle\Service\TextService;

/**
 * Class CamdramExtension
 *
 * A Twig extension which provides custom functionality that can be used in Twig templates. The extension is registered
 * in services.yml
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
            'strip_camdram_markdown' => new \Twig_Filter_Method($this, 'stripCamdramMarkdown'),
            'detect_links' => new \Twig_Filter_Method($this, 'detectLinks', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'strip_new_lines' => new \Twig_Filter_Method($this, 'stripNewLines'),
            'truncate' => new \Twig_Filter_Method($this, 'truncate', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            'plural' => new \Twig_Filter_Method($this, 'pluralize'),
        );
    }

    public function getFunctions()
    {
        return array(
            'requires_article' => new \Twig_Function_Method($this, 'requiresArticle')
        );
    }

    public function camdramMarkdown($text)
    {
        return $this->textService->convertMarkdown($text);
    }

    public function stripCamdramMarkdown($text)
    {
        return $this->textService->stripMarkdown($text);
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

    public function requiresArticle($string)
    {
        $string = strtolower($string);

        return (substr($string, -7) == 'theatre'
            || substr($string, -7) == 'society')
            && substr($string, 0, 3) != 'the';
    }

    public function pluralize($word, $number)
    {
        if ($number == 1) {
            return $word;
        } else {
            return $this->textService->pluralize($word);
        }
    }

    public function getName()
    {
        return 'camdram_extension';
    }
}
