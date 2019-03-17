<?php

namespace Acts\CamdramBundle\Service;

use Doctrine\Common\Inflector\Inflector;
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
     * @var array the regexes used to convert markdown taken from the old camdram codebase
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
     * @var array the regexes mapping markdown links to just the link text
     */
    protected $markdown_strip_regexs = array(
        '/\[L:([a-zA-Z0-9\.:\\/\_\-\?\&]+)\]/'               => '$1',
        '/\[L:([a-zA-Z0-9\.:\\/\_\-\?\&]+);([^\]]+)\]/'      => '$2',
        '/\[E:([a-zA-Z0-9\.@\_\-]+)\]/'                      => '$1',
        '/\[E:([a-zA-Z0-9\.@\_\-]+);([^\]]+)\]/'             => '$2',
        '/\[L:mailto\:([a-zA-Z0-9\.@\_\-]+)\]/'              => '$1',
        '/\[L:mailto\:([a-zA-Z0-9\.@\_\-]+);([^\]]+)\]/'     => '$2',
    );

    protected $allowed_tags = '<b><i><u><strong><em><p><ul><li><ol><br><green><red><pre><hr>';
    protected $parsedown;

    public function __construct()
    {
        $this->parsedown = new Parsedown();
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

    /**
     * Strip new lines
     *
     * @param $text
     *
     * @return mixed
     */
    public function stripNewLines($text)
    {
        return preg_replace("/[\r\n]+/", '', $text);
    }

    public function stripMarkdown($text)
    {
        $text = strip_tags($text);
        $text = preg_replace(array_keys($this->markdown_strip_regexs), array_values($this->markdown_strip_regexs), $text);

        return $text;
    }

    /**
     * Truncate a string to a certain length, adding an ellipsis only if the string was long enough to be truncated.
     *
     * @param $text
     * @param $length
     *
     * @return string
     */
    public function truncate($text, $length)
    {
        if (strlen($text) <= $length) {
            return $text;
        } else {
            return mb_substr($text, 0, $length, "UTF-8").'&hellip;';
        }
    }

    public function pluralize($word)
    {
        return Inflector::pluralize($word);
    }
}
