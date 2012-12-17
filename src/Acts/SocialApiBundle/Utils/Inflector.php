<?php
namespace Acts\SocialApiBundle\Utils;

class Inflector
{
    public function underscore($string)
    {
        return  strtolower(preg_replace('/[^A-Z^a-z^0-9]+/','_',
            preg_replace('/([a-zd])([A-Z])/','\1_\2',
                preg_replace('/([A-Z]+)([A-Z][a-z])/','\1_\2',$string))));
    }

    public function camelize($string)
    {
        return str_replace(' ','',ucwords(preg_replace('/[^A-Z^a-z^0-9]+/',' ',$string)));
    }
}