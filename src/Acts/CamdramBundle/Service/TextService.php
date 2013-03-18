<?php
namespace Acts\CamdramBundle\Service;

class TextService
{

    protected $markdown_regexs = array(
        '/\[L:(www\.[a-zA-Z0-9\.:\\/\_\-]+)\]/'             => '<a href="http://$1" rel="ext" target="_blank">$1</a>',
        '/\[L:([a-zA-Z0-9\.:\\/\_\-]+)\]/'                  => '<a href="$1" rel="ext" target="_blank">$1</a>',
        '/\[L:(www\.[a-zA-Z0-9\.:\\/\_\-]+);([^\]]+)\]/'    => '<a href="http://$1" rel="ext" target="_blank">$2</a>',
        '/\[L:([a-zA-Z0-9\.:\\/\_\-]+);([^\]]+)\]/'         => '<a href="$1" rel="ext" target="_blank">$2</a>',
        '/\[E:([a-zA-Z0-9\.@\_\-]+)\]/'                     => '<a href="mailto:$1">$1</a>',
        '/\[E:([a-zA-Z0-9\.@\_\-]+);([^\]]+)\]\]/'          => '<a href="mailto:$1">$2</a>',
        '/\[L:(mailto\:[a-zA-Z0-9\.@\_\-]+)\]/'             => '<a href="$1">$1</a>',
        '/\[L:(mailto\:[a-zA-Z0-9\.@\_\-]+);([^\]]+)\]/'    => '<a href="$1">$2</a>',
    );

    protected $allowed_tags = '<b><i><u><strong><em><p><ul><li><ol><br><green><red><pre><hr>';

    public function convertMarkdown($text, $strip_tags = true)
    {
        if ($strip_tags) $text = strip_tags($text, $this->allowed_tags);
        $text = preg_replace(array_keys($this->markdown_regexs), array_values($this->markdown_regexs), $text);
        return nl2br($text);
    }

    protected $link_regexes = array(
        '/(https?\:\\/\\/[a-zA-Z0-9%\-\_\.\\/]+)/'                      =>  '<a href="$1" rel="ext" target="_blank">$1</a>',
        '/([a-zA-Z0-9\-\_\.]@[a-zA-Z0-9\-\_\]+\.[a-zA-Z0-9\-\_\.])/' =>  '<a href="mailto:$1">$1</a>',
    );

    public function detectLinks($text)
    {
        return preg_replace(array_keys($this->link_regexes), array_values($this->link_regexes), $text);
    }

    public function stripNewLines($text)
    {
        return preg_replace("/[\r\n]+/", '', $text);
    }

    public function truncate($text, $length)
    {
        if (strlen($text) < $length) return $text;
        else return substr($text, 0, $length).'&hellip;';
    }

}