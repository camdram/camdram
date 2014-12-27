<?php
namespace Acts\CamdramBackendBundle\Service;

use Assetic\Factory\AssetFactory;

class ScssphpFilter extends \Assetic\Filter\ScssphpFilter
{

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        //disabled children processing
        return array();
    }

}