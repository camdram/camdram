<?php
namespace Acts\CamdramBundle\Service;

use Doctrine\Common\Inflector\Inflector;

/**
 * Class TextService
 *
 * Useful text processing functions. They all mainly called in templates through the associated Twig extension
 * (src/CamdramBundle/Twig/CamdramExtension.php).
 *
 * @package Acts\CamdramBundle\Service
 */
class TextService
{

    /**
     * @var array the regexes used to convert markdown taken from the old camdram codebase
     */
    protected $markdown_regexs = array(
        '/\[L:(www\.[a-zA-Z0-9\.:\\/\_\-\?\&]+)\]/'          => '<a href="http://$1" rel="ext" target="_blank">$1</a>',
        '/\[L:([a-zA-Z0-9\.:\\/\_\-\?\&]+)\]/'               => '<a href="$1" rel="ext" target="_blank">$1</a>',
        '/\[L:(www\.[a-zA-Z0-9\.:\\/\_\-\?\&]+);([^\]]+)\]/' => '<a href="http://$1" rel="ext" target="_blank">$2</a>',
        '/\[L:([a-zA-Z0-9\.:\\/\_\-\?\&]+);([^\]]+)\]/'      => '<a href="$1" rel="ext" target="_blank">$2</a>',
        '/\[E:([a-zA-Z0-9\.@\_\-]+)\]/'                      => '<a href="mailto:$1">$1</a>',
        '/\[E:([a-zA-Z0-9\.@\_\-]+);([^\]]+)\]/'             => '<a href="mailto:$1">$2</a>',
        '/\[L:mailto\:([a-zA-Z0-9\.@\_\-]+)\]/'              => '<a href="mailto:$1">$1</a>',
        '/\[L:mailto\:([a-zA-Z0-9\.@\_\-]+);([^\]]+)\]/'     => '<a href="mailto:$1">$2</a>',
    );

    protected $allowed_tags = '<b><i><u><strong><em><p><ul><li><ol><br><green><red><pre><hr>';

    /**
     * Convert the markdown format used by old camdram into HTML.
     *
     * @param string $text
     * @param bool $strip_tags whether or not to strip existing HTML tags (default is true).
     * @return string
     */
    public function convertMarkdown($text, $strip_tags = true)
    {
        if ($strip_tags) $text = strip_tags($text, $this->allowed_tags);
        $text = preg_replace(array_keys($this->markdown_regexs), array_values($this->markdown_regexs), $text);
        return nl2br($text);
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
     * @return mixed
     */
    public function stripNewLines($text)
    {
        return preg_replace("/[\r\n]+/", '', $text);
    }

    /**
     * Truncate a string to a certain length, adding an ellipsis only if the string was long enough to be truncated.
     *
     * @param $text
     * @param $length
     * @return string
     */
    public function truncate($text, $length)
    {
        if (strlen($text) <= $length) return $text;
        else return substr($text, 0, $length).'&hellip;';
    }

    public function pluralize($word)
    {
        return Inflector::pluralize($word);
    }

}
