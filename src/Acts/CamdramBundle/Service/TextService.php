<?php

namespace Acts\CamdramBundle\Service;

use Parsedown;

/**
 * Class TextService
 *
 * Useful text processing functions. They all mainly called in templates through the associated Twig extension
 * (src/CamdramBundle/Twig/CamdramExtension.php).
 */
class TextService
{
    /**
     * @var array<string,string> the regexes used to convert markdown taken from the old camdram codebase
     */
    protected $markdown_regexs = array(
        '/\[L:(www\.[a-zA-Z0-9\.:\\/\_\-\?\&]+)\]/'          => '[$1](http://$1)',
        '/\[L:([a-zA-Z0-9\.:\\/\_\-\?\&]+)\]/'               => '$1',
        '/\[L:(www\.[a-zA-Z0-9\.:\\/\_\-\?\&]+);([^\]]+)\]/' => '[$2](http://$1)',
        '/\[L:([a-zA-Z0-9\.:\\/\_\-\?\&]+);([^\]]+)\]/'      => '[$2]($1)',
        '/\[E:([a-zA-Z0-9\.@\_\-]+)\]/'                      => '[$1](mailto:$1)',
        '/\[E:([a-zA-Z0-9\.@\_\-]+);([^\]]+)\]/'             => '[$2](mailto:$1)',
        '/\[L:mailto\:([a-zA-Z0-9\.@\_\-]+)\]/'              => '[$1](mailto:$1)',
        '/\[L:mailto\:([a-zA-Z0-9\.@\_\-]+);([^\]]+)\]/'     => '[$2](mailto:$1)',
        # Temporarily making the most common HTML work
        '/<\/?b>/'                                           => '**',
        '/<\/?i>/'                                           => '*',
        '/<br ?\/?>/'                                        => "\n",
        '/<hr ?\/?>/'                                        => "\n_______\n",
        # Enforce sensble header levels in user-submitted content. h1 → h3, hn → h(n-1) for 2 ≤ n < 6.
        '/(?m)^(#{2,5}[^#])/'                                => "#$1",
        '/(?m)^#([^#])/'                                     => "###$1",
    );

    /**
     * @var array<string,string> the regexes mapping markdown links to just the link text
     */
    protected $markdown_strip_regexs = array(
        '/\[L:([a-zA-Z0-9\.:\\/\_\-\?\&]+)\]/'               => '$1',
        '/\[L:([a-zA-Z0-9\.:\\/\_\-\?\&]+);([^\]]+)\]/'      => '$2',
        '/\[E:([a-zA-Z0-9\.@\_\-]+)\]/'                      => '$1',
        '/\[E:([a-zA-Z0-9\.@\_\-]+);([^\]]+)\]/'             => '$2',
        '/\[L:mailto\:([a-zA-Z0-9\.@\_\-]+)\]/'              => '$1',
        '/\[L:mailto\:([a-zA-Z0-9\.@\_\-]+);([^\]]+)\]/'     => '$2',
    );

    /** @var string */
    protected $allowed_tags = '<b><i><u><strong><em><p><ul><li><ol><br><green><red><pre><hr>';
    /** @var Parsedown */
    protected $parsedown;

    public function __construct()
    {
        $this->parsedown = new class() extends Parsedown {
            protected function sanitiseElement(array $Element) {
                // This is called on all elements in safe mode
                if (isset($Element['name']) &&
                    ($Element['name'] == 'ul' || $Element['name'] == 'ol')) {
                    $Element['attributes']['class'] = 'prose-list';
                }
                return parent::sanitiseElement($Element);
            }
        };
        $this->parsedown->setSafeMode(true);
        $this->parsedown->setBreaksEnabled(true);
    }

    /**
     * Convert the markdown format used by old camdram into HTML.
     *
     * @param string $text
     * @param bool   $strip_tags whether or not to strip existing HTML tags (default is true).
     *
     * @return string
     */
    public function convertMarkdown($text, $strip_tags = true)
    {
        if ($strip_tags) {
            $text = strip_tags($text, $this->allowed_tags);
        }
        $text = preg_replace(array_keys($this->markdown_regexs), array_values($this->markdown_regexs), $text);

        return $this->parsedown->text($text);
    }

    /** @var array<string,string> */
    protected $link_regexes = array(
        '/((?<!")https?\:\\/\\/[a-zA-Z0-9%\-\_\.\\/\?&=]+[a-zA-Z0-9%\-\_\\/])/' =>  '<a href="$1" rel="ext" target="_blank">$1</a>',
        '/((?<!["\\/])www\.[a-zA-Z0-9%\-\_\.\\/\?&=]+[a-zA-Z0-9%\-\_\\/])/'     =>  '<a href="http://$1" rel="ext" target="_blank">$1</a>',
        '/([a-zA-Z0-9\-\_\.]+@[a-zA-Z0-9\-\_=]+\.[a-zA-Z0-9\-\_\.]+)/'      =>  '<a href="mailto:$1">$1</a>',
    );

    /**
     * Detect URLs and email addresses in a string and automatically turn them into links
     *
     * @param string $text
     *
     * @return string
     */
    public function detectLinks($text)
    {
        return preg_replace(array_keys($this->link_regexes), array_values($this->link_regexes), $text);
    }

    public function stripNewLines(string $text): string
    {
        return preg_replace("/[\r\n]+/", '', $text);
    }

    /**
     * @param string $text
     * @return string
     */
    public function stripMarkdown($text)
    {
        $text = strip_tags($text);
        $text = preg_replace(array_keys($this->markdown_strip_regexs), array_values($this->markdown_strip_regexs), $text);
        $text = $this->parsedown->text($text);
        return html_entity_decode(strip_tags($text));
    }

    /**
     * Truncate a string to a certain length, adding an ellipsis only if the string
     * was long enough to be truncated.
     */
    public function truncate($text, $length): string
    {
        if (mb_strlen($text, "UTF-8") <= $length) {
            return $text;
        } else {
            return mb_substr($text, 0, $length, "UTF-8").'…';
        }
    }

    /**
     * Truncate HTML to a certain length, adding an ellipsis only if the string
     * was long enough to be truncated.
     */
    public function truncateHTML(string $html, int $targetLength): string {
        // There's a huge number of ways to do this. This is a state machine
        // parsing the HTML constructs normally found in the body.
        $opts = LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD;
        // escape multibyte characters
        $html_ascii = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        // A length *estimator* for HTML.
        $truncated = false;
        $len = 0;
        $state = 0; // text, whitespace, entity, <, tag, attr", attr', comment (7-11)
        $totlen = strlen($html_ascii);
        for ($i = 0; $i < $totlen; $i++) {
            $char = $html_ascii[$i];
            reconsume:
            switch ($state) {
            case 0:   // data state i.e. normal text
                if (ctype_space($char)) {
                    $len++;
                    $state = 1;
                } else if ($char === '&') {
                    $len++;
                    $state = 2;
                } else if ($char === '<') {
                    $state = 3;
                } else {
                    $len++;
                }
                break;
            case 1:   // whitespace folding
                if (ctype_space($char)) break;
                $state = 0;
                goto reconsume;
                break;
            case 2:   // entity
                if ($char === ';') $state = 0;
                break;
            case 3:   // <
                if (ctype_alpha($char) || $char == '/') {
                    $state = 4;
                } else if ($char === '!') {
                    $state = 7;
                } else {
                    $len++; // Count the < as text
                    $state = 0;
                    goto reconsume;
                }
            case 4:   // in tag, doubles as bogus comment state
                if ($char === '>') $state = 0;
                if ($char === '"') $state = 5;
                if ($char === "'") $state = 6;
                break;
            case 5:   // in double-quoted attribute
                if ($char === '"') $state = 4;
                break;
            case 6:   // in single-quoted attribute
                if ($char === "'") $state = 4;
                break;
            case 7:   // <!
            case 8:   // <!-
                if ($char === '-') $state++;
                else $state = 4;
                break;
            case 9:   // <!-- comment
            case 10:  // <!-- comment -
                if ($char === '-') $state++;
                else $state = 9;
                break;
            case 11:  // <!-- comment --
                $state = ($char === '>') ? 0 : 9;
                break;
            }

            // Aim to keep whole words.
            if (($len > $targetLength - 2 && $state == 1) || $len > $targetLength + 2) {
                $truncated = true;
                break;
            }
        }
        if (!$truncated) return $html;

        // Get libxml2 to close all our open tags, etc.
        $doc = new \DOMDocument();
        $doc->loadHTML("<div>".substr($html_ascii, 0, $i), $opts);
        $result = trim($doc->saveHTML());
        if (mb_substr($result, 0, 5, 'UTF-8') === '<div>' && mb_substr($result, -6, NULL, 'UTF-8') === '</div>') {
            $result = mb_substr($result, 5, mb_strlen($result, 'UTF-8') - 11, 'UTF-8');
        }
        // Put the … to the left of any closing tags.
        return preg_replace('/(<\/\s*[A-Za-z0-9]*\s*>|\s)*$/', '…\0', $result, 1);
    }
}
