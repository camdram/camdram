<?php
namespace Acts\CamdramBackendBundle\Service;

use Zend\Mail\Storage\Part;

class EmailParser
{
    public $rawEmail;

    public function __construct($raw)
    {
        //echo($raw);die();
        $this->rawEmail = new \Zend\Mail\Storage\Message(array('raw' => $raw));
    }

    public function getPartByType($type)
    {
        foreach (new \RecursiveIteratorIterator($this->rawEmail) as $part) {
            if (strpos($part->contentType, $type) !== false) {
                $text = $part->getContent();
                if ($part->contentTransferEncoding == 'base64') {
                    return base64_decode($text);
                } else {
                    return $text;
                }
            }
        }
        return false;
    }

    public function getTextPart()
    {
        if ($this->rawEmail->isMultipart()) {
            $text = $this->getPartByType('text/plain');
            if (!$text) {
                $text = $this->getPartByType('text/html');
                return strip_tags($text);
            }
            return $text;
        } else {
            return $this->rawEmail->getContent();
        }
    }

    public function getRawTo()
    {
        return $this->rawEmail->to;
    }

    /**
     * @return \Zend\Mail\Header\To
     */
    public function getTo()
    {
        return $this->rawEmail->getHeaders()->get('to');
    }

    public function getRawFrom()
    {
        return $this->rawEmail->from;
    }

    /**
     * @return \Zend\Mail\Header\From
     */
    public function getFrom()
    {
        return $this->rawEmail->getHeaders()->get('from');
    }

    public function getRawCc()
    {
        if ($this->rawEmail->getHeaders()->has('cc')) {
            return $this->rawEmail->cc;
        }
        else {
            return '';
        }
    }

    /**
     * @return \Zend\Mail\Header\Cc
     */
    public function getCc()
    {
        if ($this->rawEmail->getHeaders()->has('cc')) {
            return $this->rawEmail->getHeaders()->get('cc');
        }
        else {
            return null;
        }
    }

    public function getSubject()
    {
        return $this->rawEmail->subject;
    }
}