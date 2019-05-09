<?php

namespace Acts\CamdramBundle\Twig;

use Acts\CamdramBundle\Service\TextService;
use Twig\Extension\AbstractExtension;

/**
 * Class CamdramExtension
 *
 * A Twig extension which provides custom functionality that can be used in Twig templates. The extension is registered
 * in services.yml
 */
class CamdramExtension extends AbstractExtension
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
            new \Twig_SimpleFilter('camdram_markdown', [$this, 'camdramMarkdown'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('strip_camdram_markdown', [$this, 'stripCamdramMarkdown']),
            new \Twig_SimpleFilter('detect_links', [$this, 'detectLinks'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
            new \Twig_SimpleFilter('strip_new_lines', [$this, 'stripNewLines']),
            new \Twig_SimpleFilter('truncate', [$this, 'truncate'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
            new \Twig_SimpleFilter('plural', [$this, 'pluralize']),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('requires_article', [$this, 'requiresArticle'])
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
