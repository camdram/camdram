<?php

namespace Acts\CamdramBundle\Entity;

/**
 * Play (or other theatrical work)
 *
 */
class Play
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getMid()
    {
        return $this->data['id'];
    }

    protected function getValue($key, $array = false)
    {
        if ($array) {
            return $this->data['property'][$key]['values'];
        } else {
            return $this->data['property'][$key]['values'][0];
        }
    }

    public function getName()
    {
        return $this->getValue('/type/object/name');
    }

    public function getDescription()
    {
        return $this->getValue('/common/topic/description');
    }

    public function getAuthor()
    {
        return $this->getValue('/book/written_work/author', true);
    }

    public function getPlaywright()
    {
        return $this->getValue('/theater/play/playwright', true);
    }

    public function getComposer()
    {
        return $this->getValue('/theater/play/composer', true);
    }

    public function getLyricist()
    {
        return $this->getValue('/theater/play/lyricist', true);
    }

    public function getOrchestrator()
    {
        return $this->getValue('/theater/play/orchestrator', true);
    }

    public function getCharacters()
    {
        return $this->getValue('/theater/play/characters', true);
    }

    public function getSoundtracks()
    {
        return $this->getValue('/theater/play/soundtracks', true);
    }

    public function getProductions()
    {
        return $this->getValue('/theater/play/productions', true);
    }

    public function getUrl()
    {
        return 'https://www.freebase.com/'.$this->getMid();
    }

}
