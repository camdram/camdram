<?php

namespace Acts\CamdramBundle\Twig;

use Acts\CamdramBundle\Service\TextService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    private $router;

    public function __construct(TextService $textService, UrlGeneratorInterface $router)
    {
        $this->textService = $textService;
        $this->router = $router;
    }

    /**
     * Defines the custom Twig filters
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig\TwigFilter('camdram_markdown', [$this, 'camdramMarkdown'], ['is_safe' => ['html']]),
            new \Twig\TwigFilter('strip_camdram_markdown', [$this, 'stripCamdramMarkdown']),
            new \Twig\TwigFilter('detect_links', [$this, 'detectLinks'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
            new \Twig\TwigFilter('strip_new_lines', [$this, 'stripNewLines']),
            new \Twig\TwigFilter('truncate', [$this, 'truncate']),
            new \Twig\TwigFilter('truncateHTML', [$this, 'truncateHTML'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
            new \Twig\TwigFilter('plural', [$this, 'pluralize']),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig\TwigFunction('link_entity', [$this, 'link_entity'], ['is_safe' => ['html']]),
            new \Twig\TwigFunction('list_sep_verb', [$this, 'list_sep_verb']),
            new \Twig\TwigFunction('requires_article', [$this, 'requiresArticle']),
            new \Twig\TwigFunction('wcag_colors', [$this, 'genWcagColors'])
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

    public function truncateHTML($html, $length)
    {
        return $this->textService->truncateHTML($html, $length);
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

    public function list_sep_verb(array $loop, string $verb_pl = null, string $verb_sing = null): string
    {
        if ($loop['revindex'] > 2)  return ', ';
        if ($loop['revindex'] == 2) return ' and ';
        if ($verb_pl == null)    return '';

        return ($loop['length'] == 1) ?
            ' '.($verb_sing ?? ($verb_pl.'s')).' ' :
            " $verb_pl ";
    }

    /**
     * Create a link to an entity which implements getEntityType(), getName()
     * and getSlug().
     * Options:
     *  - innerhtml
     *  - innertext
     */
    public function link_entity(object $entity, array $options = []): string
    {
        $url = $this->router->generate("get_{$entity->getEntityType()}", [
            'identifier' => $entity->getSlug() ]);
        // Escape all non-alphabetic <= 0xFF.
        $url = mb_encode_numericentity($url, [
            [0x00, 0x40, 0, 0],
            [0x5B, 0x60, 0, 0],
            [0x7B, 0xFF, 0, 0]
        ]);
        $content = $options['innerhtml'] ??
            htmlspecialchars($options['innertext'] ?? $entity->getName());
        return "<a href=\"$url\">$content</a>";
    }

    public function getName()
    {
        return 'camdram_extension';
    }

    /**
     * This function takes a six-digit hex color and returns an array
     * of hex colors which are accessible. E.g.
     * offer a contrast ratio of 3:1 and 4.5:1 against white, as defined at
     * WCAG 2.0 § 1.4.3 (https://www.w3.org/TR/WCAG20/#visual-audio-contrast-contrast).
     */
    public function genWcagColors(string $hexColor): array
    {
        $num = hexdec($hexColor);
        $result = ['raw' => '#' . substr('000000' . dechex($num), -6)];
        $rgb = [$num >> 16, ($num >> 8) & 255, $num & 255];
        $strictMinLum = 0.1;    # 3:1 against black.
        $strictMaxLum = 0.1833; # Level AA, < 18pt text vs. white.
        // In the interest of a simple algorithm I am deliberately mixing
        // two color models here. Multiplying $rgb by a constant lightens
        // it in the HSL model, while the luminance value it's being compared
        // to is based on a far more sophisticated approach.

        $rawMaxLum    = 0.5;       # Arbitrary luminance cap.
        // $laxMaxLum = 0.3;       # Level AA, ≥ 18pt text vs. white.
        while ($rawMaxLum < self::rgbToLum($rgb)) {
            $rgb[0] *= 0.95; $rgb[1] *= 0.95; $rgb[2] *= 0.95;
        }
        $result['raw'] = '#' . substr('000000' . dechex((int)$rgb[0] << 16 | (int)$rgb[1] << 8 | (int)$rgb[2]), -6);
        while ($strictMaxLum < self::rgbToLum($rgb)) {
            $rgb[0] *= 0.95; $rgb[1] *= 0.95; $rgb[2] *= 0.95;
        }
        while ($strictMinLum > self::rgbToLum($rgb)) {
            // This is mixing with white not lightening, so it won't infinite loop for black.
            // The 255 cap is present for blue only; no other color can overflow.
            $rgb[0] += 5; $rgb[1] += 5; $rgb[2] = min($rgb[2] + 5, 255);
        }
        $result['smalltext'] = '#' . substr('000000' . dechex((int)$rgb[0] << 16 | (int)$rgb[1] << 8 | (int)$rgb[2]), -6);
        return $result;
    }

    private static function rgbToLum(array $rgb): float {
        $RGB = [];
        for ($i = 0; $i < 3; $i++) {
            $RGB[] = $rgb[$i] <= 10 ? $rgb[$i] / 3294.6 : (($rgb[$i] + 14.025) / 269.025) ** 2.4;
        }
        return 0.2126*$RGB[0] + 0.7152*$RGB[1] + 0.0722*$RGB[2];
    }
}
